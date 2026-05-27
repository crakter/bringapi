<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Reports;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Enum\ReportFormat;
use Bring\Api\Http\HttpMethod;

/**
 * POST https://www.mybring.com/reports/api/generate/{customerNumber}/{reportTypeId}.{format}
 *
 * @extends AbstractJsonEndpoint<GenerateReportResponse>
 */
final class GenerateReportEndpoint extends AbstractJsonEndpoint
{
    /** @param array<string, mixed> $parameters */
    public function __construct(
        private readonly string $customerNumber,
        private readonly string $reportTypeId,
        private readonly array $parameters = [],
        private readonly ReportFormat $format = ReportFormat::JSON,
    ) {
    }

    public function method(): HttpMethod
    {
        return HttpMethod::POST;
    }

    protected function baseUri(): string
    {
        return sprintf(
            'https://www.mybring.com/reports/api/generate/%s/%s.%s',
            rawurlencode($this->customerNumber),
            rawurlencode($this->reportTypeId),
            $this->format->value,
        );
    }

    /** @return array<mixed, mixed>|null */
    protected function jsonBody(): ?array
    {
        return $this->parameters === [] ? null : $this->parameters;
    }

    /** @param array<mixed, mixed> $decoded */
    protected function parseDecoded(array $decoded): GenerateReportResponse
    {
        return GenerateReportResponse::fromArray($decoded);
    }
}
