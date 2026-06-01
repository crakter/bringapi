<?php

declare(strict_types=1);

namespace Bring\Api\Tests\Endpoint\PickupPoint;

use Bring\Api\ApiClient;
use Bring\Api\Auth\Credentials;
use Bring\Api\Endpoint\PickupPoint\PickupPointApi;
use Bring\Api\Enum\Country;
use Bring\Api\Exception\InvalidArgumentException;
use Bring\Api\Tests\Support\RecordingClient;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PickupPointApi::class)]
final class PickupPointApiTest extends TestCase
{
    public function testAllByCountryHitsCorrectUrl(): void
    {
        $client = new RecordingClient([new Response(200, [], '{"pickupPoint":[{"id":"1","name":"Posten Majorstuen","visitingAddress":"Foo 1","visitingPostalCode":"0367","visitingCity":"Oslo","countryCode":"NO","location":{"latitude":59.93,"longitude":10.71}}]}')]);
        $api = ApiClient::withCredentials(new Credentials('me@example.com', 'k'), $client);

        $resp = $api->pickupPoint()->all(Country::NO);

        self::assertSame(
            'https://api.bring.com/pickuppoint/api/pickuppoint/NO/all.json',
            (string) $client->lastRequest()->getUri(),
        );
        self::assertCount(1, $resp->pickupPoints);
        $pp = $resp->pickupPoints[0];
        self::assertSame('1', $pp->id);
        self::assertSame('Posten Majorstuen', $pp->name);
        self::assertSame('Oslo', $pp->city);
        self::assertSame(59.93, $pp->latitude);
    }

    public function testByLocationFormatsCoordinates(): void
    {
        $client = new RecordingClient([new Response(200, [], '{"pickupPoint":[]}')]);
        $api = ApiClient::withCredentials(new Credentials('me@example.com', 'k'), $client);

        $api->pickupPoint()->byLocation(Country::SE, 59.331, 18.064);

        $uri = (string) $client->lastRequest()->getUri();
        self::assertStringEndsWith('/SE/location/59.331,18.064.json', $uri);
    }

    public function testUnsupportedCountryRejected(): void
    {
        $api = ApiClient::withCredentials(new Credentials('me@example.com', 'k'), new RecordingClient([]));

        $this->expectException(InvalidArgumentException::class);
        $api->pickupPoint()->all(Country::DE);
    }
}
