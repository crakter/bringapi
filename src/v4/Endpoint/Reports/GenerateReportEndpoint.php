<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Reports;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Http\HttpMethod;

/**
 * POST https://www.mybring.com/reports/api/generate/{customerNumber}/{reportTypeId}.json
 *
 * Hardcoded to JSON: this endpoint extends AbstractJsonEndpoint, so
 * non-JSON responses would fail to parse here. The format of the
 * eventual report file is controlled separately by the parameters
 * payload, not by the request URL suffix.
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
    ) {
    }

    #[\Override]
    public function method(): HttpMethod
    {
        return HttpMethod::POST;
    }

    #[\Override]
    protected function baseUri(): string
    {
        return sprintf(
            'https://www.mybring.com/reports/api/generate/%s/%s.json',
            rawurlencode($this->customerNumber),
            rawurlencode($this->reportTypeId),
        );
    }

    /** @return array<mixed, mixed>|null */
    #[\Override]
    protected function jsonBody(): ?array
    {
        return $this->parameters === [] ? null : $this->parameters;
    }

    /** @param array<mixed, mixed> $decoded */
    #[\Override]
    protected function parseDecoded(array $decoded): GenerateReportResponse
    {
        return GenerateReportResponse::fromArray($decoded);
    }
}
