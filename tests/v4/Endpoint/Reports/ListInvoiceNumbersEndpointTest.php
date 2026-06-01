<?php

declare(strict_types=1);

namespace Bring\Api\Tests\Endpoint\Reports;

use Bring\Api\ApiClient;
use Bring\Api\Auth\Credentials;
use Bring\Api\Endpoint\Reports\ListInvoiceNumbersEndpoint;
use Bring\Api\Exception\InvalidArgumentException;
use Bring\Api\Tests\Support\RecordingClient;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ListInvoiceNumbersEndpoint::class)]
final class ListInvoiceNumbersEndpointTest extends TestCase
{
    private function client(): RecordingClient
    {
        return new RecordingClient([new Response(200, ['Content-Type' => 'application/json'], '{"invoices":[]}')]);
    }

    public function testNoDateRangeEmitsNoQuery(): void
    {
        $client = $this->client();
        $api = ApiClient::withCredentials(new Credentials('me@example.com', 'k'), $client);

        $api->reports()->listInvoiceNumbers('PARCELS_NORWAY-00012341234');

        $uri = (string) $client->lastRequest()->getUri();
        self::assertSame(
            'https://www.mybring.com/invoicearchive/api/invoices/PARCELS_NORWAY-00012341234.json',
            $uri,
        );
    }

    public function testDateRangeFormatsAsBringExpectedDotDdMmYyyy(): void
    {
        $client = $this->client();
        $api = ApiClient::withCredentials(new Credentials('me@example.com', 'k'), $client);

        $api->reports()->listInvoiceNumbers(
            'PARCELS_NORWAY-00012341234',
            fromDate: new \DateTimeImmutable('2026-01-15'),
            toDate: new \DateTimeImmutable('2026-05-30'),
        );

        $uri = (string) $client->lastRequest()->getUri();
        // Bring expects dd.mm.yyyy, not ISO 8601 — make sure we don't accidentally
        // send 2026-01-15 (would silently return an empty result with no error).
        self::assertStringContainsString('fromDate=15.01.2026', $uri);
        self::assertStringContainsString('toDate=30.05.2026', $uri);
    }

    public function testBooleanFiltersAreEmittedAsLowercaseStrings(): void
    {
        $client = $this->client();
        $api = ApiClient::withCredentials(new Credentials('me@example.com', 'k'), $client);

        $api->reports()->listInvoiceNumbers(
            'PARCELS_NORWAY-00012341234',
            onlyWithSpecification: true,
            onlyProcessed: false,
        );

        $uri = (string) $client->lastRequest()->getUri();
        self::assertStringContainsString('invoicesWithSpecification=true', $uri);
        self::assertStringContainsString('onlyProcessedInvoices=false', $uri);
    }

    public function testRejectsInvertedDateRange(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/fromDate must be on or before toDate/');
        new ListInvoiceNumbersEndpoint(
            'PARCELS_NORWAY-00012341234',
            fromDate: new \DateTimeImmutable('2026-05-30'),
            toDate: new \DateTimeImmutable('2026-01-15'),
        );
    }

    public function testAcceptsAnyDateTimeInterfaceImplementation(): void
    {
        $client = $this->client();
        $api = ApiClient::withCredentials(new Credentials('me@example.com', 'k'), $client);

        // Mutable DateTime is a legitimate caller pattern (callers may already
        // hold one from a date-picker library).
        $api->reports()->listInvoiceNumbers(
            'X',
            fromDate: new \DateTime('2026-04-01'),
            toDate: new \DateTime('2026-04-30'),
        );

        self::assertStringContainsString('fromDate=01.04.2026', (string) $client->lastRequest()->getUri());
    }
}
