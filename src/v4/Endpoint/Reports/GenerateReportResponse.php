<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Reports;

final class GenerateReportResponse
{
    public function __construct(
        public readonly ?string $reportId,
        public readonly ?string $statusUrl,
        /** @var array<mixed, mixed> */
        public readonly array $raw,
    ) {
    }

    /** @param array<mixed, mixed> $decoded */
    public static function fromArray(array $decoded): self
    {
        return new self(
            reportId: isset($decoded['reportId']) ? (string) $decoded['reportId'] : null,
            statusUrl: isset($decoded['statusUrl']) ? (string) $decoded['statusUrl'] : null,
            raw: $decoded,
        );
    }
}
