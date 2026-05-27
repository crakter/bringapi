<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\PickupPoint;

final class PickupPointResponse
{
    public function __construct(
        public readonly ?PickupPoint $pickupPoint,
        /** @var array<mixed, mixed> */
        public readonly array $raw,
    ) {
    }

    /** @param array<mixed, mixed> $decoded */
    public static function fromArray(array $decoded): self
    {
        $item = $decoded['pickupPoint'] ?? $decoded;
        if (is_array($item) && isset($item['id'])) {
            return new self(PickupPoint::fromArray($item), $decoded);
        }

        return new self(null, $decoded);
    }
}
