<?php

declare(strict_types=1);

namespace Bring\Api\Tests\Endpoint\Tracking;

use Bring\Api\ApiClient;
use Bring\Api\Auth\Credentials;
use Bring\Api\Endpoint\Tracking\SignatureEndpoint;
use Bring\Api\Endpoint\Tracking\TrackingApi;
use Bring\Api\Exception\InvalidArgumentException;
use Bring\Api\Tests\Support\RecordingClient;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TrackingApi::class)]
#[CoversClass(SignatureEndpoint::class)]
final class TrackingApiTest extends TestCase
{
    public function testTrackParsesNestedEventsIncludingSignatureLink(): void
    {
        $payload = json_encode([
            'consignmentSet' => [[
                'consignmentId' => 'TESTPACKAGE',
                'packageSet' => [[
                    'packageNumber' => 'TESTPACKAGE-1',
                    'statusDescription' => 'Delivered',
                    'eventSet' => [[
                        'dateIso' => '2026-05-27T10:15:00+02:00',
                        'description' => 'Levert',
                        'status' => 'DELIVERED',
                        'city' => 'Oslo',
                        'countryCode' => 'NO',
                        'signatureLink' => 'api/signatur.png?kollinummer=370123456789&dateTimeIso=2026-05-27T10:15:00%2B02:00',
                    ]],
                ]],
            ]],
        ]);

        self::assertNotFalse($payload);
        $client = new RecordingClient([new Response(200, ['Content-Type' => 'application/json'], $payload)]);
        $api = ApiClient::withCredentials(new Credentials('me@example.com', 'k'), $client);

        $resp = $api->tracking()->track('TESTPACKAGE');

        $latest = $resp->latestEvent();
        self::assertNotNull($latest);
        self::assertSame('Levert', $latest->description);
        self::assertSame('DELIVERED', $latest->status);
        self::assertSame(
            'api/signatur.png?kollinummer=370123456789&dateTimeIso=2026-05-27T10:15:00%2B02:00',
            $latest->signatureLink,
        );
    }

    public function testSignatureFetchesPngBytesFromSignatureLinkPath(): void
    {
        $png = "\x89PNG\r\n\x1a\nFAKEBYTES";
        $client = new RecordingClient([new Response(200, ['Content-Type' => 'image/png'], $png)]);
        $api = ApiClient::withCredentials(new Credentials('me@example.com', 'k'), $client);

        $bytes = $api->tracking()->signature(
            'api/signatur.png?kollinummer=370123456789&dateTimeIso=2026-05-27T10:15:00%2B02:00',
        );

        self::assertSame($png, $bytes);
        $uri = (string) $client->lastRequest()->getUri();
        self::assertSame(
            'https://www.mybring.com/tracking/api/signatur.png?kollinummer=370123456789&dateTimeIso=2026-05-27T10:15:00%2B02:00',
            $uri,
            'the signatureLink path must be appended verbatim — its query string is part of the contract',
        );
        self::assertSame('image/png', $client->lastRequest()->getHeaderLine('Accept'));
    }

    public function testSignatureUrlReturnsFullUrlWithoutFetching(): void
    {
        $client = new RecordingClient([]);
        $api = ApiClient::withCredentials(new Credentials('me@example.com', 'k'), $client);

        $url = $api->tracking()->signatureUrl(
            'api/signatur.png?kollinummer=370123456789&dateTimeIso=2026-05-27T10:15:00%2B02:00',
        );

        self::assertSame(
            'https://www.mybring.com/tracking/api/signatur.png?kollinummer=370123456789&dateTimeIso=2026-05-27T10:15:00%2B02:00',
            $url,
        );
        self::assertCount(0, $client->requests, 'signatureUrl() must not perform an HTTP request');
    }

    public function testSignatureUrlNormalisesLeadingSlash(): void
    {
        $client = new RecordingClient([]);
        $api = ApiClient::withCredentials(new Credentials('me@example.com', 'k'), $client);

        // Bring sometimes returns the path with a leading slash, sometimes without.
        $with = $api->tracking()->signatureUrl('/api/signatur.png?kollinummer=1');
        $without = $api->tracking()->signatureUrl('api/signatur.png?kollinummer=1');

        self::assertSame($with, $without);
    }

    public function testSignatureRejectsEmptyPath(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new SignatureEndpoint('');
    }
}
