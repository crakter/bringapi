<?php

declare(strict_types=1);

namespace Bring\Api\Tests\Endpoint\Address;

use Bring\Api\ApiClient;
use Bring\Api\Auth\Credentials;
use Bring\Api\Endpoint\Address\AddressApi;
use Bring\Api\Enum\Country;
use Bring\Api\Tests\Support\RecordingClient;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AddressApi::class)]
final class AddressApiTest extends TestCase
{
    public function testPostalCodeLookupHitsCountryScopedUrl(): void
    {
        $client = new RecordingClient([new Response(200, ['Content-Type' => 'application/json'], '{"postal_code":"0150","city":"OSLO","municipality":"OSLO","postal_code_type":"NORMAL"}')]);
        $api = ApiClient::withCredentials(new Credentials('me@example.com', 'k'), $client);

        $resp = $api->address()->postalCode(Country::NO, '0150');

        self::assertSame('https://api.bring.com/address/api/no/postal-codes/0150', (string) $client->lastRequest()->getUri());
        self::assertSame('OSLO', $resp->city);
        self::assertSame('NORMAL', $resp->postalCodeType);
    }

    public function testSuggestionsUsesQueryParam(): void
    {
        $client = new RecordingClient([new Response(200, [], '{"postal_codes":[{"postal_code":"0150","city":"OSLO"}]}')]);
        $api = ApiClient::withCredentials(new Credentials('me@example.com', 'k'), $client);

        $resp = $api->address()->suggestions(Country::NO, '015');

        $uri = (string) $client->lastRequest()->getUri();
        self::assertStringStartsWith('https://api.bring.com/address/api/no/postal-codes/suggestions', $uri);
        self::assertStringContainsString('postal_code=015', $uri);
        self::assertCount(1, $resp->matches);
    }
}
