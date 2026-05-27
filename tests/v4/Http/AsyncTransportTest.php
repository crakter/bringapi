<?php

declare(strict_types=1);

namespace Bring\Api\Tests\Http;

use Bring\Api\ApiClient;
use Bring\Api\Auth\Credentials;
use Bring\Api\Endpoint\Shipping\PriceEndpoint;
use Bring\Api\Endpoint\Shipping\PriceRequest;
use Bring\Api\Enum\Country;
use Bring\Api\Exception\BringApiException;
use Bring\Api\Exception\InvalidArgumentException;
use Bring\Api\Http\AsyncTransport;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AsyncTransport::class)]
final class AsyncTransportTest extends TestCase
{
    private function makeRequest(): PriceRequest
    {
        return new PriceRequest(
            fromCountry: Country::NO,
            fromPostalCode: '0150',
            toCountry: Country::NO,
            toPostalCode: '5003',
            packages: [['weightInGrams' => 1000]],
        );
    }

    public function testFanOutResolvesAllInParallel(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{"Product":[{"productId":"BUSINESS_PARCEL","price":{"amountWithVAT":{"value":"100"}}}]}'),
            new Response(200, [], '{"Product":[{"productId":"BUSINESS_PARCEL","price":{"amountWithVAT":{"value":"105"}}}]}'),
            new Response(200, [], '{"Product":[{"productId":"BUSINESS_PARCEL","price":{"amountWithVAT":{"value":"110"}}}]}'),
        ]);
        $guzzle = new Client(['handler' => HandlerStack::create($mock)]);
        $bring = ApiClient::withCredentials(new Credentials('me@example.com', 'k'), $guzzle, retry: false);

        $promises = [];
        for ($i = 0; $i < 3; $i++) {
            $promises[] = $bring->async()->send(new PriceEndpoint($this->makeRequest()));
        }
        $results = Utils::all($promises)->wait();

        self::assertCount(3, $results);
        self::assertSame(100.0, $results[0]->products[0]->priceWithVat);
        self::assertSame(105.0, $results[1]->products[0]->priceWithVat);
        self::assertSame(110.0, $results[2]->products[0]->priceWithVat);
    }

    public function testAllHelperShortCircuitsOnFirstError(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{"Product":[]}'),
            new Response(500, ['Content-Type' => 'application/json'], '{"errors":[{"code":"BOOM","description":"crash"}]}'),
            new Response(200, [], '{"Product":[]}'),
        ]);
        $guzzle = new Client(['handler' => HandlerStack::create($mock)]);
        $bring = ApiClient::withCredentials(new Credentials('me@example.com', 'k'), $guzzle, retry: false);

        $endpoints = [
            new PriceEndpoint($this->makeRequest()),
            new PriceEndpoint($this->makeRequest()),
            new PriceEndpoint($this->makeRequest()),
        ];

        $this->expectException(BringApiException::class);
        $bring->async()->all($endpoints)->wait();
    }

    public function testSettleAllKeepsGoingOnError(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{"Product":[]}'),
            new Response(500, ['Content-Type' => 'application/json'], '{"errors":[{"code":"BOOM"}]}'),
            new Response(200, [], '{"Product":[]}'),
        ]);
        $guzzle = new Client(['handler' => HandlerStack::create($mock)]);
        $bring = ApiClient::withCredentials(new Credentials('me@example.com', 'k'), $guzzle, retry: false);

        $results = $bring->async()->settleAll([
            new PriceEndpoint($this->makeRequest()),
            new PriceEndpoint($this->makeRequest()),
            new PriceEndpoint($this->makeRequest()),
        ])->wait();

        self::assertSame('fulfilled', $results[0]['state']);
        self::assertSame('rejected', $results[1]['state']);
        self::assertInstanceOf(BringApiException::class, $results[1]['reason']);
        self::assertSame('fulfilled', $results[2]['state']);
    }
}
