<?php

declare(strict_types=1);

namespace Bring\Api\Tests\Endpoint\Reports;

use Bring\Api\Endpoint\Reports\ReportStatusResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(ReportStatusResponse::class)]
final class ReportStatusResponseTest extends TestCase
{
    public function testDoneReportIsReady(): void
    {
        // Bring's real "finished" status is DONE (see the legacy
        // StatusOfReport::checkStatus() written against the live API).
        $response = ReportStatusResponse::fromArray(['status' => 'DONE']);

        self::assertTrue($response->isReady());
    }

    public function testNotDoneReportIsNotReady(): void
    {
        // NOT_DONE is what Bring returns while the report is still generating.
        $response = ReportStatusResponse::fromArray(['status' => 'NOT_DONE']);

        self::assertFalse($response->isReady());
    }

    /**
     * Regression: the previous implementation only recognised COMPLETED / READY —
     * values Bring never returns — so every polled report looked perpetually
     * unfinished and pending-report tables filled with rows that were never
     * collected.
     */
    #[DataProvider('provideStatusesThatBringNeverReadiesOn')]
    public function testStatusesBringNeverReturnsAreNotReady(string $status): void
    {
        self::assertFalse(ReportStatusResponse::fromArray(['status' => $status])->isReady());
    }

    /** @return iterable<string, array{string}> */
    public static function provideStatusesThatBringNeverReadiesOn(): iterable
    {
        yield 'completed (never emitted by Bring)' => ['COMPLETED'];
        yield 'ready (never emitted by Bring)' => ['READY'];
        yield 'empty' => [''];
    }

    public function testIsReadyIsCaseInsensitive(): void
    {
        self::assertTrue(ReportStatusResponse::fromArray(['status' => 'done'])->isReady());
    }

    public function testMissingStatusIsNotReady(): void
    {
        $response = ReportStatusResponse::fromArray([]);

        self::assertSame('', $response->status);
        self::assertFalse($response->isReady());
    }

    public function testDownloadUrlFallsBackToXmlUrl(): void
    {
        // The real status.json exposes the finished report as xmlUrl / xlsUrl,
        // not downloadUrl, so the parsed URL must fall back through them.
        $response = ReportStatusResponse::fromArray([
            'status' => 'DONE',
            'xmlUrl' => 'https://www.mybring.com/reports/api/report/abc.xml',
            'xlsUrl' => 'https://www.mybring.com/reports/api/report/abc.xls',
        ]);

        self::assertSame('https://www.mybring.com/reports/api/report/abc.xml', $response->downloadUrl);
    }

    public function testExplicitDownloadUrlWins(): void
    {
        $response = ReportStatusResponse::fromArray([
            'status' => 'DONE',
            'downloadUrl' => 'https://example.test/explicit',
            'xmlUrl' => 'https://www.mybring.com/reports/api/report/abc.xml',
        ]);

        self::assertSame('https://example.test/explicit', $response->downloadUrl);
    }

    public function testDownloadUrlNullWhenAbsent(): void
    {
        $response = ReportStatusResponse::fromArray(['status' => 'NOT_DONE']);

        self::assertNull($response->downloadUrl);
    }
}
