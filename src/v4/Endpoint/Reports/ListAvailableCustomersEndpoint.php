<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Reports;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Http\HttpMethod;

/**
 * GET https://www.mybring.com/reports/api/generate.json
 *
 * Hardcoded to JSON: this endpoint extends AbstractJsonEndpoint, so
 * other formats would fail to parse here.
 *
 * @extends AbstractJsonEndpoint<GenericReportResponse>
 */
final class ListAvailableCustomersEndpoint extends AbstractJsonEndpoint
{
    #[\Override]
    public function method(): HttpMethod
    {
        return HttpMethod::GET;
    }

    #[\Override]
    protected function baseUri(): string
    {
        return 'https://www.mybring.com/reports/api/generate.json';
    }

    /** @param array<mixed, mixed> $decoded */
    #[\Override]
    protected function parseDecoded(array $decoded): GenericReportResponse
    {
        return GenericReportResponse::fromArray($decoded);
    }
}
