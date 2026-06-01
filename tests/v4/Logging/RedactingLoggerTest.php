<?php

declare(strict_types=1);

namespace Bring\Api\Tests\Logging;

use Bring\Api\Http\HeaderNames;
use Bring\Api\Logging\RedactingLogger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

#[CoversClass(RedactingLogger::class)]
final class RedactingLoggerTest extends TestCase
{
    private LoggerInterface $captured;

    /** @var list<array{level:string, message:string, context:array<mixed, mixed>}> */
    private array $sink = [];

    #[\Override]
    protected function setUp(): void
    {
        $sink = &$this->sink;
        $this->captured = new class ($sink) extends AbstractLogger {
            public function __construct(private array &$sink)
            {
            }

            #[\Override]
            public function log($level, \Stringable|string $message, array $context = []): void
            {
                $this->sink[] = ['level' => (string) $level, 'message' => (string) $message, 'context' => $context];
            }
        };
    }

    public function testRedactsHeaderValues(): void
    {
        $logger = new RedactingLogger($this->captured);
        $logger->info('outgoing request', [
            'headers' => [
                HeaderNames::AUTH_KEY => 'sk-1234',
                HeaderNames::AUTH_UID => 'me@example.com',
                'Accept' => 'application/json',
            ],
        ]);

        $entry = $this->sink[0];
        self::assertSame('***redacted***', $entry['context']['headers'][HeaderNames::AUTH_KEY]);
        self::assertSame('***redacted***', $entry['context']['headers'][HeaderNames::AUTH_UID]);
        self::assertSame('application/json', $entry['context']['headers']['Accept']);
    }

    public function testRedactsLiteralSecretsAcrossMessageAndContext(): void
    {
        $logger = new RedactingLogger($this->captured, ['sk-supersecret']);
        $logger->warning('request failed: sk-supersecret was rejected', [
            'body' => 'echo: sk-supersecret',
            'nested' => ['key' => 'sk-supersecret'],
        ]);

        $entry = $this->sink[0];
        self::assertStringNotContainsString('sk-supersecret', $entry['message']);
        self::assertStringNotContainsString('sk-supersecret', $entry['context']['body']);
        self::assertStringNotContainsString('sk-supersecret', $entry['context']['nested']['key']);
    }

    public function testWithSecretReturnsNewInstance(): void
    {
        $base = new RedactingLogger($this->captured);
        $augmented = $base->withSecret('top-secret');
        self::assertNotSame($base, $augmented);
        $augmented->info('contains top-secret');
        self::assertStringNotContainsString('top-secret', $this->sink[0]['message']);
    }

    public function testRedactsNestedHeaderTrees(): void
    {
        $logger = new RedactingLogger($this->captured);
        $logger->debug('round-trip', [
            'request_headers' => [HeaderNames::AUTH_KEY => 'a'],
            'response_headers' => [HeaderNames::AUTH_KEY => 'b'],
        ]);
        $entry = $this->sink[0];
        self::assertSame('***redacted***', $entry['context']['request_headers'][HeaderNames::AUTH_KEY]);
        self::assertSame('***redacted***', $entry['context']['response_headers'][HeaderNames::AUTH_KEY]);
    }
}
