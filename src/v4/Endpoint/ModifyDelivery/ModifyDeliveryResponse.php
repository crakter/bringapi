<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\ModifyDelivery;

final class ModifyDeliveryResponse
{
    public function __construct(
        public readonly string $status,
        public readonly ?string $modificationId,
        /** @var array<mixed, mixed> */
        public readonly array $raw,
    ) {
    }

    /** @param array<mixed, mixed> $decoded */
    public static function fromArray(array $decoded): self
    {
        return new self(
            status: (string) ($decoded['status'] ?? 'OK'),
            modificationId: isset($decoded['modificationId']) ? (string) $decoded['modificationId'] : null,
            raw: $decoded,
        );
    }
}
