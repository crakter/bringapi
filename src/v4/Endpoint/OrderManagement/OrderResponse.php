<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\OrderManagement;

final class OrderResponse
{
    public function __construct(
        public readonly ?string $orderNumber,
        public readonly ?string $status,
        /** @var array<mixed, mixed> */
        public readonly array $raw,
    ) {
    }

    /** @param array<mixed, mixed> $decoded */
    public static function fromArray(array $decoded): self
    {
        return new self(
            orderNumber: isset($decoded['orderNumber']) ? (string) $decoded['orderNumber'] : null,
            status: isset($decoded['status']) ? (string) $decoded['status'] : null,
            raw: $decoded,
        );
    }
}
