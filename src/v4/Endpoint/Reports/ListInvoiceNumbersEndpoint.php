<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Reports;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Http\HttpMethod;

/**
 * GET https://www.mybring.com/invoicearchive/api/invoices/{customerNumberOrGroupId}.json
 *
 * Hardcoded to JSON: this endpoint extends AbstractJsonEndpoint, so
 * other formats would fail to parse here.
 *
 * @extends AbstractJsonEndpoint<GenericReportResponse>
 */
final class ListInvoiceNumbersEndpoint extends AbstractJsonEndpoint
{
    public function __construct(private readonly string $customerNumberOrGroupId)
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
            'https://www.mybring.com/invoicearchive/api/invoices/%s.json',
            rawurlencode($this->customerNumberOrGroupId),
        );
    }

    /** @param array<mixed, mixed> $decoded */
    #[\Override]
    protected function parseDecoded(array $decoded): GenericReportResponse
    {
        return GenericReportResponse::fromArray($decoded);
    }
}
