<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Booking;

final class BookingResponse
{
    /**
     * @param list<BookedConsignment> $consignments
     * @param array<mixed, mixed>     $raw
     */
    public function __construct(
        public readonly array $consignments,
        public readonly array $raw,
    ) {
    }

    /** @param array<mixed, mixed> $decoded */
    public static function fromArray(array $decoded): self
    {
        $items = $decoded['consignments'] ?? [];
        $out = [];
        if (is_array($items)) {
            foreach ($items as $c) {
                if (is_array($c)) {
                    $out[] = BookedConsignment::fromArray($c);
                }
            }
        }

        return new self($out, $decoded);
    }
}
