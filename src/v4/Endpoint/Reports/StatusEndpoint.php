<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Reports;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Http\HttpMethod;

/**
 * GET https://www.mybring.com/reports/api/report/{reportId}/status.json
 *
 * Hardcoded to JSON: this endpoint extends AbstractJsonEndpoint, so
 * non-JSON responses (which the Reports API supports for some routes)
 * would fail to parse here. Use {@see DownloadEndpoint} for raw bytes.
 *
 * @extends AbstractJsonEndpoint<ReportStatusResponse>
 */
final class StatusEndpoint extends AbstractJsonEndpoint
{
    public function __construct(private readonly string $reportId)
    {
    }

    #[\Override]
    public function method(): HttpMethod
    {
        return HttpMethod::GET;
    }

    #[\Override]
    protected function baseUri(): string
    {
        return sprintf(
            'https://www.mybring.com/reports/api/report/%s/status.json',
            rawurlencode($this->reportId),
        );
    }

    /** @param array<mixed, mixed> $decoded */
    #[\Override]
    protected function parseDecoded(array $decoded): ReportStatusResponse
    {
        return ReportStatusResponse::fromArray($decoded);
    }
}
