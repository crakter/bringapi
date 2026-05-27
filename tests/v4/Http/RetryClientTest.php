<?php

declare(strict_types=1);

namespace Bring\Api\Tests\Http;

use Bring\Api\Http\RetryClient;
use Bring\Api\Retry\ExponentialBackoff;
use Bring\Api\Retry\RecordingSleeper;
use Bring\Api\Tests\Support\RecordingClient;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;

#[CoversClass(RetryClient::class)]
#[CoversClass(ExponentialBackoff::class)]
final class RetryClientTest extends TestCase
{
    private HttpFactory $factory;

    #[\Override]
    protected function setUp(): void
    {
        $this->factory = new HttpFactory();
    }

    public function testSucceedsOnFirstTryWithoutSleeping(): void
    {
        $sleeper = new RecordingSleeper();
        $inner = new RecordingClient([new Response(200, [], 'ok')]);
        $client = new RetryClient($inner, maxAttempts: 4, sleeper: $sleeper);

        $response = $client->sendRequest($this->factory->createRequest('GET', 'https://api.bring.com/'));

        self::assertSame(200, $response->getStatusCode());
        self::assertCount(1, $inner->requests);
        self::assertSame([], $sleeper->slept);
    }

    public function testRetriesOn503ThenSucceeds(): void
    {
        $sleeper = new RecordingSleeper();
        $inner = new RecordingClient([
            new Response(503, [], 'busy'),
            new Response(503, [], 'still busy'),
            new Response(200, [], 'ok'),
        ]);
        $client = new RetryClient($inner, maxAttempts: 4, sleeper: $sleeper, backoff: new ExponentialBackoff(1.0, 8.0, fn () => 1.0));

        $resp = $client->sendRequest($this->factory->createRequest('GET', 'https://api.bring.com/'));

        self::assertSame(200, $resp->getStatusCode());
        self::assertCount(3, $inner->requests);
        self::assertCount(2, $sleeper->slept);
        // Exponential: 1.0 * 1, 1.0 * 2 (with rng = 1.0 so no jitter reduction)
        self::assertSame(1.0, $sleeper->slept[0]);
        self::assertSame(2.0, $sleeper->slept[1]);
    }

    public function testHonoursRetryAfterHeader(): void
    {
        $sleeper = new RecordingSleeper();
        $inner = new RecordingClient([
            new Response(429, ['Retry-After' => '7'], 'rate limited'),
            new Response(200, [], 'ok'),
        ]);
        $client = new RetryClient($inner, sleeper: $sleeper, backoff: new ExponentialBackoff(1.0, 30.0, fn () => 1.0));

        $client->sendRequest($this->factory->createRequest('GET', 'https://api.bring.com/'));

        // Retry-After (7s) wins over backoff (1s).
        self::assertSame([7.0], $sleeper->slept);
    }

    public function testGivesUpAfterMaxAttempts(): void
    {
        $sleeper = new RecordingSleeper();
        $inner = new RecordingClient([
            new Response(503),
            new Response(503),
            new Response(503),
        ]);
        $client = new RetryClient($inner, maxAttempts: 3, sleeper: $sleeper, backoff: new ExponentialBackoff(0.1, 1.0, fn () => 0.0));

        $resp = $client->sendRequest($this->factory->createRequest('GET', 'https://api.bring.com/'));

        self::assertSame(503, $resp->getStatusCode());
        self::assertCount(3, $inner->requests, '3 attempts total');
        self::assertCount(2, $sleeper->slept, 'sleeps happen *between* attempts');
    }

    public function testRetriesOnTransportExceptionThenRethrows(): void
    {
        $sleeper = new RecordingSleeper();
        $boom = new class('network down') extends \RuntimeException implements ClientExceptionInterface {
        };
        $inner = new RecordingClient([$boom, $boom]);
        $client = new RetryClient($inner, maxAttempts: 2, sleeper: $sleeper, backoff: new ExponentialBackoff(0.0, 0.0, fn () => 0.0));

        $this->expectException(ClientExceptionInterface::class);
        $client->sendRequest($this->factory->createRequest('GET', 'https://api.bring.com/'));
    }

    public function testNonRetryableStatusReturnsImmediately(): void
    {
        $sleeper = new RecordingSleeper();
        $inner = new RecordingClient([new Response(404)]);
        $client = new RetryClient($inner, sleeper: $sleeper);

        $resp = $client->sendRequest($this->factory->createRequest('GET', 'https://api.bring.com/'));

        self::assertSame(404, $resp->getStatusCode());
        self::assertCount(1, $inner->requests);
        self::assertSame([], $sleeper->slept);
    }

    public function testRejectsInvalidMaxAttempts(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new RetryClient(new RecordingClient([]), maxAttempts: 0);
    }

    public function testExponentialBackoffJitterWithinBounds(): void
    {
        $backoff = new ExponentialBackoff(1.0, 10.0);
        for ($attempt = 1; $attempt <= 6; $attempt++) {
            $delay = $backoff->delaySeconds($attempt);
            self::assertGreaterThanOrEqual(0.0, $delay);
            self::assertLessThanOrEqual(10.0, $delay, "attempt $attempt must be capped at maxDelay");
        }
    }
}
