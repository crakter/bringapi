<?php

declare(strict_types=1);

namespace Bring\Api\Tests\Endpoint\Booking;

use Bring\Api\ApiClient;
use Bring\Api\Auth\Credentials;
use Bring\Api\Dto\Address;
use Bring\Api\Dto\Contact;
use Bring\Api\Dto\Package;
use Bring\Api\Endpoint\Booking\BookingRequest;
use Bring\Api\Enum\AdditionalService;
use Bring\Api\Enum\Country;
use Bring\Api\Enum\Product;
use Bring\Api\Http\HeaderNames;
use Bring\Api\Tests\Support\RecordingClient;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(\Bring\Api\Endpoint\Booking\BookingApi::class)]
final class BookingApiTest extends TestCase
{
    public function testBookSerialisesAndPostsToCorrectUrl(): void
    {
        $client = new RecordingClient([new Response(200, [], '{"consignments":[{"confirmation":{"consignmentNumber":"BR-1"}}]}')]);
        $api = ApiClient::withCredentials(new Credentials('me@example.com', 'k'), $client);

        $sender = new Address(
            name: 'Sender Co',
            addressLine: 'Sandakerveien 24c',
            addressLine2: null,
            postalCode: '0473',
            city: 'Oslo',
            countryCode: Country::NO,
            contact: new Contact('Eve Sender', 'eve@example.com'),
        );
        $recipient = new Address(
            name: 'Recipient Co',
            addressLine: 'Storgata 1',
            addressLine2: null,
            postalCode: '5003',
            city: 'Bergen',
            countryCode: Country::NO,
        );
        $request = BookingRequest::single(
            schemaVersion: '1',
            customerNumber: 'PARCELS_NORWAY-10001234567',
            product: Product::HOME_DELIVERY_PARCEL,
            sender: $sender,
            recipient: $recipient,
            packages: [new Package(weightInKg: 2)],
            additional: [AdditionalService::EVARSLING],
            testIndicator: true,
        );

        $resp = $api->booking()->book($request);

        $req = $client->lastRequest();
        self::assertSame('POST', $req->getMethod());
        self::assertSame('https://api.bring.com/booking/api/booking', (string) $req->getUri());
        self::assertSame('true', $req->getHeaderLine(HeaderNames::TEST_MODE));

        $body = json_decode((string) $req->getBody(), true);
        self::assertSame('1', $body['schemaVersion']);
        self::assertSame('PARCELS_NORWAY-10001234567', $body['customerNumber']);
        self::assertTrue($body['testIndicator']);
        self::assertSame(Product::HOME_DELIVERY_PARCEL->value, $body['consignments'][0]['product']['id']);
        self::assertSame('EVARSLING', $body['consignments'][0]['product']['additionalServices'][0]['id']);
        self::assertSame('Sender Co', $body['consignments'][0]['parties']['sender']['name']);
        self::assertSame('NO', $body['consignments'][0]['parties']['recipient']['countryCode']);

        self::assertSame('BR-1', $resp->consignments[0]->confirmation);
    }
}
