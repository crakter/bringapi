<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Reports;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Enum\ReportFormat;
use Bring\Api\Http\HttpMethod;

/**
 * GET https://www.mybring.com/invoicearchive/api/invoices/{customerNumberOrGroupId}.{format}
 *
 * @extends AbstractJsonEndpoint<GenericReportResponse>
 */
final class ListInvoiceNumbersEndpoint extends AbstractJsonEndpoint
{
    public function __construct(
        private readonly string $customerNumberOrGroupId,
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
            'https://www.mybring.com/invoicearchive/api/invoices/%s.%s',
            rawurlencode($this->customerNumberOrGroupId),
            $this->format->value,
        );
    }

    /** @param array<mixed, mixed> $decoded */
    #[\Override]
    protected function parseDecoded(array $decoded): GenericReportResponse
    {
        return GenericReportResponse::fromArray($decoded);
    }
}
