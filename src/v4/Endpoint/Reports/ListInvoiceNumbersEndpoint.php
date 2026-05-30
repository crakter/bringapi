<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Reports;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Http\HttpMethod;

/**
 * GET https://www.mybring.com/invoicearchive/api/invoices/{customerNumberOrGroupId}.json
 *
 * Bring's invoice archive accepts date-range filtering via two query
 * parameters (Bring's own format — dd.mm.yyyy, not ISO 8601). Without
 * any filter, callers get every invoice Bring has on file for the
 * customer/group, which can be hundreds of MB for large accounts. Pass
 * a {@see \DateTimeInterface} range to scope the export.
 *
 *   $bring->reports()->listInvoiceNumbers(
 *       'PARCELS_NORWAY-00012341234',
 *       fromDate: new \DateTimeImmutable('-1 month'),
 *       toDate: new \DateTimeImmutable(),
 *   );
 *
 * @extends AbstractJsonEndpoint<GenericReportResponse>
 */
final class ListInvoiceNumbersEndpoint extends AbstractJsonEndpoint
{
    /** Bring's documented date format for this endpoint. */
    private const DATE_FORMAT = 'd.m.Y';

    public function __construct(
        private readonly string $customerNumberOrGroupId,
        private readonly ?\DateTimeInterface $fromDate = null,
        private readonly ?\DateTimeInterface $toDate = null,
        private readonly ?bool $onlyWithSpecification = null,
        private readonly ?bool $onlyProcessed = null,
    ) {
        if ($fromDate !== null && $toDate !== null && $fromDate > $toDate) {
            throw new \Bring\Api\Exception\InvalidArgumentException(
                'ListInvoiceNumbersEndpoint: fromDate must be on or before toDate.',
            );
        }
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

    /** @return array<string, string> */
    #[\Override]
    protected function queryParameters(): array
    {
        $q = [];
        if ($this->fromDate !== null) {
            $q['fromDate'] = $this->fromDate->format(self::DATE_FORMAT);
        }
        if ($this->toDate !== null) {
            $q['toDate'] = $this->toDate->format(self::DATE_FORMAT);
        }
        if ($this->onlyWithSpecification !== null) {
            $q['invoicesWithSpecification'] = $this->onlyWithSpecification ? 'true' : 'false';
        }
        if ($this->onlyProcessed !== null) {
            $q['onlyProcessedInvoices'] = $this->onlyProcessed ? 'true' : 'false';
        }

        return $q;
    }

    /** @param array<mixed, mixed> $decoded */
    #[\Override]
    protected function parseDecoded(array $decoded): GenericReportResponse
    {
        return GenericReportResponse::fromArray($decoded);
    }
}
