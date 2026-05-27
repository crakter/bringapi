<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Reports;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Enum\ReportFormat;
use Bring\Api\Http\HttpMethod;

/**
 * GET https://www.mybring.com/reports/api/report/{reportId}/status.{format}
 *
 * @extends AbstractJsonEndpoint<ReportStatusResponse>
 */
final class StatusEndpoint extends AbstractJsonEndpoint
{
    public function __construct(
        private readonly string $reportId,
        private readonly ReportFormat $format = ReportFormat::JSON,
    ) {
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
            'https://www.mybring.com/reports/api/report/%s/status.%s',
            rawurlencode($this->reportId),
            $this->format->value,
        );
    }

    /** @param array<mixed, mixed> $decoded */
    #[\Override]
    protected function parseDecoded(array $decoded): ReportStatusResponse
    {
        return ReportStatusResponse::fromArray($decoded);
    }
}
