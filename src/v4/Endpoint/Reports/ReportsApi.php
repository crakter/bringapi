<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Reports;

use Bring\Api\Enum\ReportFormat;
use Bring\Api\Http\Transport;

/**
 * Reports API — https://developer.bring.com/api/reports/.
 *
 * Bring's documented rate limit is 20 concurrent requests per customer; the
 * library does not enforce this — callers should pace their own polling.
 */
final class ReportsApi
{
    public function __construct(private readonly Transport $transport)
    {
    }

    public function listAvailableCustomers(): GenericReportResponse
    {
        return $this->transport->send(new ListAvailableCustomersEndpoint());
    }

    public function listAvailableReportsForCustomer(string $customerNumber): GenericReportResponse
    {
        return $this->transport->send(new ListAvailableReportsForCustomerEndpoint($customerNumber));
    }

    /** @param array<string, mixed> $parameters report-type-specific filter parameters (date ranges, etc.) */
    public function generate(string $customerNumber, string $reportTypeId, array $parameters = []): GenerateReportResponse
    {
        return $this->transport->send(new GenerateReportEndpoint($customerNumber, $reportTypeId, $parameters));
    }

    public function status(string $reportId): ReportStatusResponse
    {
        return $this->transport->send(new StatusEndpoint($reportId));
    }

    /**
     * Returns the raw response body — this is the only Reports route where
     * the format genuinely matters: callers may want XLS bytes or an HTML
     * blob, not a JSON envelope.
     */
    public function download(string $reportId, ReportFormat $format = ReportFormat::JSON): string
    {
        return $this->transport->send(new DownloadEndpoint($reportId, $format));
    }

    public function listInvoiceNumbers(string $customerNumberOrGroupId): GenericReportResponse
    {
        return $this->transport->send(new ListInvoiceNumbersEndpoint($customerNumberOrGroupId));
    }
}
