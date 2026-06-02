<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Reports;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Http\HttpMethod;

/**
 * GET https://www.mybring.com/reports/api/generate/{customerNumber}/{reportTypeId}.json
 *
 * Bring's report-generation route is a GET with the report-type filters
 * passed as query parameters (the sibling list-available routes on the same
 * /reports/api/generate base path are GET too). Issuing a POST here makes
 * Bring's gateway reject the request with 405 Method Not Allowed and an
 * empty body before the response ever carries an error envelope.
 *
 * Hardcoded to JSON: this endpoint extends AbstractJsonEndpoint, so
 * non-JSON responses would fail to parse here. The format of the
 * eventual report file is controlled separately by the parameters,
 * not by the request URL suffix.
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
        return HttpMethod::GET;
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

    /**
     * Report filters are passed through verbatim, except booleans: Bring
     * expects the literal strings "true"/"false", but PHP's http_build_query
     * would serialise a bool as 1/0, which Bring's validation rejects. Mirrors
     * the explicit bool handling in {@see ListInvoiceNumbersEndpoint}.
     *
     * @return array<string, mixed>
     */
    #[\Override]
    protected function queryParameters(): array
    {
        return array_map(
            static fn (mixed $value): mixed => is_bool($value) ? ($value ? 'true' : 'false') : $value,
            $this->parameters,
        );
    }

    /** @param array<mixed, mixed> $decoded */
    #[\Override]
    protected function parseDecoded(array $decoded): GenerateReportResponse
    {
        return GenerateReportResponse::fromArray($decoded);
    }
}
