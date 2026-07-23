<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Reports;

final class ReportStatusResponse
{
    public function __construct(
        public readonly string $status,
        public readonly ?string $downloadUrl,
        /** @var array<mixed, mixed> */
        public readonly array $raw,
    ) {
    }

    /**
     * True once Bring has finished generating the report.
     *
     * Bring's Reports API reports progress as `NOT_DONE` while the report is
     * still building and `DONE` once it is ready to download (see the legacy
     * {@see \Crakter\BringApi\Clients\Reports\StatusOfReport::checkStatus()},
     * written against the live API). The earlier `COMPLETED`/`READY` check
     * never matched a real response, so every polled report looked perpetually
     * unfinished and callers' pending-report tables filled with rows that were
     * never collected.
     */
    public function isReady(): bool
    {
        return strtoupper($this->status) === 'DONE';
    }

    /** @param array<mixed, mixed> $decoded */
    public static function fromArray(array $decoded): self
    {
        // Bring returns the finished report's location as `xmlUrl` / `xlsUrl`
        // (there is no `downloadUrl` field); fall back through them so the
        // parsed URL isn't perpetually null on a real response.
        $downloadUrl = $decoded['downloadUrl'] ?? $decoded['xmlUrl'] ?? $decoded['xlsUrl'] ?? null;

        return new self(
            status: (string) ($decoded['status'] ?? ''),
            downloadUrl: null !== $downloadUrl ? (string) $downloadUrl : null,
            raw: $decoded,
        );
    }
}
