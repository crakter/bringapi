<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\OrderManagement;

final class PackagingListResponse
{
    public function __construct(
        public readonly bool $accepted,
        /** @var array<mixed, mixed> */
        public readonly array $raw,
    ) {
    }

    /** @param array<mixed, mixed> $decoded */
    public static function fromArray(array $decoded): self
    {
        return new self(
            accepted: (bool) ($decoded['accepted'] ?? ($decoded['status'] ?? null) === 'OK'),
            raw: $decoded,
        );
    }
}
