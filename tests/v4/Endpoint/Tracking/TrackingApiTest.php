<?php

declare(strict_types=1);

namespace Bring\Api\Tests\Endpoint\Tracking;

use Bring\Api\ApiClient;
use Bring\Api\Auth\Credentials;
use Bring\Api\Endpoint\Tracking\TrackingApi;
use Bring\Api\Tests\Support\RecordingClient;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TrackingApi::class)]
final class TrackingApiTest extends TestCase
{
    public function testTrackParsesNestedEvents(): void
    {
        $payload = json_encode([
            'consignmentSet' => [[
                'consignmentId' => 'TESTPACKAGE',
                'packageSet' => [[
                    'packageNumber' => 'TESTPACKAGE-1',
                    'statusDescription' => 'In transit',
                    'eventSet' => [[
                        'dateIso' => '2026-05-27T10:15:00+02:00',
                        'description' => 'Sortert',
                        'status' => 'SORTED',
                        'city' => 'Oslo',
                        'countryCode' => 'NO',
                    ]],
                ]],
            ]],
        ]);

        $client = new RecordingClient([new Response(200, ['Content-Type' => 'application/json'], $payload)]);
        $api = ApiClient::withCredentials(new Credentials('me@example.com', 'k'), $client);

        $resp = $api->tracking()->track('TESTPACKAGE');

        $latest = $resp->latestEvent();
        self::assertNotNull($latest);
        self::assertSame('Sortert', $latest->description);
        self::assertSame('Oslo', $latest->city);
        self::assertSame('SORTED', $latest->status);
        self::assertNotNull($latest->dateIso);
        self::assertSame('2026-05-27', $latest->dateIso->format('Y-m-d'));
    }
}
