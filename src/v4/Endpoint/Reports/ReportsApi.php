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

    public function listAvailableCustomers(ReportFormat $format = ReportFormat::JSON): GenericReportResponse
    {
        return $this->transport->send(new ListAvailableCustomersEndpoint($format));
    }

    public function listAvailableReportsForCustomer(string $customerNumber, ReportFormat $format = ReportFormat::JSON): GenericReportResponse
    {
        return $this->transport->send(new ListAvailableReportsForCustomerEndpoint($customerNumber, $format));
    }

    /** @param array<string, mixed> $parameters report-type-specific filter parameters (date ranges, etc.) */
    public function generate(string $customerNumber, string $reportTypeId, array $parameters = [], ReportFormat $format = ReportFormat::JSON): GenerateReportResponse
    {
        return $this->transport->send(new GenerateReportEndpoint($customerNumber, $reportTypeId, $parameters, $format));
    }

    public function status(string $reportId, ReportFormat $format = ReportFormat::JSON): ReportStatusResponse
    {
        return $this->transport->send(new StatusEndpoint($reportId, $format));
    }

    /** Returns the raw response body (for XLS/XML/HTML/JSON file types). */
    public function download(string $reportId, ReportFormat $format = ReportFormat::JSON): string
    {
        return $this->transport->send(new DownloadEndpoint($reportId, $format));
    }

    public function listInvoiceNumbers(string $customerNumberOrGroupId, ReportFormat $format = ReportFormat::JSON): GenericReportResponse
    {
        return $this->transport->send(new ListInvoiceNumbersEndpoint($customerNumberOrGroupId, $format));
    }
}
