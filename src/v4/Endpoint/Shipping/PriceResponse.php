<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Shipping;

/**
 * Parsed response from /shippingguide/v2/products/price.
 *
 * Bring returns either {"consignments":[…]} (v2) or {"Product":[…]} (legacy).
 * Both shapes are folded into {@see $products}; callers walk the typed list.
 *
 * The original decoded array is preserved on {@see $raw} for callers that
 * need a field the typed projection doesn't expose yet.
 */
final class PriceResponse
{
    /**
     * @param list<ProductPrice>     $products
     * @param array<mixed, mixed>    $raw
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
