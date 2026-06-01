<?php

declare(strict_types=1);

namespace Bring\Api\Dto;

use Bring\Api\Enum\Country;

final class Address
{
    public function __construct(
        public readonly string $name,
        public readonly string $addressLine,
        public readonly ?string $addressLine2,
        public readonly string $postalCode,
        public readonly string $city,
        public readonly Country $countryCode,
        public readonly ?string $reference = null,
        public readonly ?string $additionalAddressInfo = null,
        public readonly ?Contact $contact = null,
    ) {
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $a = [
            'name' => $this->name,
            'addressLine' => $this->addressLine,
            'postalCode' => $this->postalCode,
            'city' => $this->city,
            'countryCode' => $this->countryCode->value,
        ];
        if ($this->addressLine2 !== null) {
            $a['addressLine2'] = $this->addressLine2;
        }
        if ($this->reference !== null) {
            $a['reference'] = $this->reference;
        }
        if ($this->additionalAddressInfo !== null) {
            $a['additionalAddressInfo'] = $this->additionalAddressInfo;
        }
        if ($this->contact !== null) {
            $a['contact'] = $this->contact->toArray();
        }

        return $a;
    }
}
