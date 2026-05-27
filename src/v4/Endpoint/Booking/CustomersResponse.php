<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Booking;

final class CustomersResponse
{
    /**
     * @param list<array{customerNumber:string, name:string}> $customers
     * @param array<mixed, mixed>                              $raw
     */
    public function __construct(
        public readonly array $customers,
        public readonly array $raw,
    ) {
    }

    /** @param array<mixed, mixed> $decoded */
    public static function fromArray(array $decoded): self
    {
        $items = $decoded['customers'] ?? $decoded;
        $out = [];
        if (is_array($items)) {
            foreach ($items as $c) {
                if (is_array($c) && isset($c['customerNumber'])) {
                    $out[] = [
                        'customerNumber' => (string) $c['customerNumber'],
                        'name' => (string) ($c['name'] ?? $c['customerName'] ?? ''),
                    ];
                }
            }
        }

        return new self($out, $decoded);
    }
}
