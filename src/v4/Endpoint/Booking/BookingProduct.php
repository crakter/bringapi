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
        $a = ['id' => $this->id->value];
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
