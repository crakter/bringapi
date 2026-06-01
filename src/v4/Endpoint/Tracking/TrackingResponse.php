<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Tracking;

final class TrackingResponse
{
    /**
     * @param list<TrackedConsignment> $consignments
     * @param array<mixed, mixed>      $raw
     */
    public function __construct(
        public readonly array $consignments,
        public readonly array $raw,
    ) {
    }

    /** @param array<mixed, mixed> $decoded */
    public static function fromArray(array $decoded): self
    {
        $items = $decoded['consignmentSet'] ?? $decoded['consignments'] ?? [];
        $consignments = [];
        if (is_array($items)) {
            foreach ($items as $item) {
                if (is_array($item)) {
                    $consignments[] = TrackedConsignment::fromArray($item);
                }
            }
        }

        return new self($consignments, $decoded);
    }

    /** Convenience: most recent event on the first package of the first consignment. */
    public function latestEvent(): ?TrackedEvent
    {
        return $this->consignments[0]->packages[0]->events[0] ?? null;
    }
}
