<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Booking;

use Bring\Api\Enum\AdditionalService;
use Bring\Api\Enum\Product;

final class BookingProduct
{
    /**
     * @param Product                 $id
     * @param list<AdditionalService> $additionalServices
     * @param array<string, mixed>    $customsDeclaration  free-form customs payload (Bring schema varies by product)
     */
    public function __construct(
        public readonly Product $id,
        public readonly array $additionalServices = [],
        public readonly array $customsDeclaration = [],
    ) {
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        // Booking API wants the numeric service code in product.id, not the
        // string enum value the Shipping Guide uses (see Product::bookingProductId
        // for the why). Falls back to the string value for products without a
        // known numeric mapping.
        $a = ['id' => $this->id->bookingProductId()];
        if ($this->additionalServices !== []) {
            $a['additionalServices'] = array_map(
                static fn (AdditionalService $s): array => ['id' => $s->value],
                $this->additionalServices,
            );
        }
        if ($this->customsDeclaration !== []) {
            $a['customsDeclaration'] = $this->customsDeclaration;
        }

        return $a;
    }
}
