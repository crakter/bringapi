<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Reports;

final class ReportStatusResponse
{
    public function __construct(
        public readonly string $status,
        public readonly ?string $downloadUrl,
        /** @var array<mixed, mixed> */
        public readonly array $raw,
    ) {
    }

    public function isReady(): bool
    {
        return strtoupper($this->status) === 'COMPLETED' || strtoupper($this->status) === 'READY';
    }

    /** @param array<mixed, mixed> $decoded */
    public static function fromArray(array $decoded): self
    {
        return new self(
            status: (string) ($decoded['status'] ?? ''),
            downloadUrl: isset($decoded['downloadUrl']) ? (string) $decoded['downloadUrl'] : null,
            raw: $decoded,
        );
    }
}
