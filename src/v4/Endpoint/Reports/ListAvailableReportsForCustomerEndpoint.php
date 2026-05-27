<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Reports;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Enum\ReportFormat;
use Bring\Api\Http\HttpMethod;

/**
 * GET https://www.mybring.com/reports/api/generate/{customerNumber}.{format}
 *
 * @extends AbstractJsonEndpoint<GenericReportResponse>
 */
final class ListAvailableReportsForCustomerEndpoint extends AbstractJsonEndpoint
{
    public function __construct(
        private readonly string $customerNumber,
        private readonly ReportFormat $format = ReportFormat::JSON,
    ) {
    }

    public function method(): HttpMethod
    {
        return HttpMethod::GET;
    }

    protected function baseUri(): string
    {
        return sprintf(
            'https://www.mybring.com/reports/api/generate/%s.%s',
            rawurlencode($this->customerNumber),
            $this->format->value,
        );
    }

    /** @param array<mixed, mixed> $decoded */
    protected function parseDecoded(array $decoded): GenericReportResponse
    {
        return GenericReportResponse::fromArray($decoded);
    }
}
