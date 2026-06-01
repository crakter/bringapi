<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Shipping;

final class DeliveryTimeResponse
{
    /**
     * @param list<ProductPrice>  $products
     * @param array<mixed, mixed> $raw
     */
    public function __construct(
        public readonly array $products,
        public readonly array $raw,
    ) {
    }

    /** @param array<mixed, mixed> $decoded */
    public static function fromArray(array $decoded): self
    {
        $products = [];
        $items = $decoded['consignments'][0]['products']
            ?? $decoded['Product']
            ?? $decoded['products']
            ?? [];
        if (is_array($items)) {
            foreach ($items as $item) {
                if (is_array($item)) {
                    $products[] = ProductPrice::fromArray($item);
                }
            }
        }

        return new self($products, $decoded);
    }
}
