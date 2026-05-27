<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\PickupPoint;

final class PickupPointListResponse
{
    /**
     * @param list<PickupPoint>   $pickupPoints
     * @param array<mixed, mixed> $raw
     */
    public function __construct(
        public readonly array $pickupPoints,
        public readonly array $raw,
    ) {
    }

    /** @param array<mixed, mixed> $decoded */
    public static function fromArray(array $decoded): self
    {
        $items = $decoded['pickupPoint'] ?? $decoded['pickupPoints'] ?? [];
        $points = [];
        if (is_array($items)) {
            foreach ($items as $item) {
                if (is_array($item)) {
                    $points[] = PickupPoint::fromArray($item);
                }
            }
        }

        return new self($points, $decoded);
    }
}
