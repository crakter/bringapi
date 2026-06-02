<?php

declare(strict_types=1);

namespace Bring\Api\Tests\Endpoint\Reports;

use Bring\Api\ApiClient;
use Bring\Api\Auth\Credentials;
use Bring\Api\Endpoint\Reports\GenerateReportEndpoint;
use Bring\Api\Tests\Support\RecordingClient;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(GenerateReportEndpoint::class)]
final class GenerateReportEndpointTest extends TestCase
{
    private function client(): RecordingClient
    {
        return new RecordingClient([
            new Response(200, ['Content-Type' => 'application/json'], '{"reportId":"abc"}'),
        ]);
    }

    public function testUsesGetNotPost(): void
    {
        // Regression: Bring's /reports/api/generate route only accepts GET.
        // A POST is rejected with 405 Method Not Allowed (empty body).
        $client = $this->client();
        $api = ApiClient::withCredentials(new Credentials('me@example.com', 'k'), $client);

        $api->reports()->generate('PARCELS_NORWAY-00012341234', 'MASTER-SPECIFIED_INVOICE');

        self::assertSame('GET', $client->lastRequest()->getMethod());
    }

    public function testHardcodesJsonUrl(): void
    {
        $client = $this->client();
        $api = ApiClient::withCredentials(new Credentials('me@example.com', 'k'), $client);

        $api->reports()->generate('PARCELS_NORWAY-00012341234', 'MASTER-SPECIFIED_INVOICE');

        self::assertStringStartsWith(
            'https://www.mybring.com/reports/api/generate/PARCELS_NORWAY-00012341234/MASTER-SPECIFIED_INVOICE.json',
            (string) $client->lastRequest()->getUri(),
        );
    }

    public function testParametersGoIntoQueryStringNotBody(): void
    {
        $client = $this->client();
        $api = ApiClient::withCredentials(new Credentials('me@example.com', 'k'), $client);

        $api->reports()->generate(
            'PARCELS_NORWAY-00012341234',
            'MASTER-SPECIFIED_INVOICE',
            ['invoiceNumber' => '12345'],
        );

        $request = $client->lastRequest();
        self::assertStringContainsString('invoiceNumber=12345', (string) $request->getUri());
        // A GET carries no JSON body — the params must not leak into one.
        self::assertSame('', (string) $request->getBody());
    }
}
