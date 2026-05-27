<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\PickupPoint;

/** Single pickup point entry. */
final class PickupPoint
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $address,
        public readonly ?string $postalCode,
        public readonly ?string $city,
        public readonly ?string $countryCode,
        public readonly ?float $latitude,
        public readonly ?float $longitude,
        public readonly ?string $openingHoursToday,
        /** @var array<mixed, mixed> */
        public readonly array $raw,
    ) {
    }

    /** @param array<mixed, mixed> $a */
    public static function fromArray(array $a): self
    {
        $loc = $a['location'] ?? $a['coordinates'] ?? [];

        return new self(
            id: (string) ($a['id'] ?? ''),
            name: (string) ($a['name'] ?? ''),
            address: isset($a['visitingAddress']) ? (string) $a['visitingAddress'] : (isset($a['address']) ? (string) $a['address'] : null),
            postalCode: isset($a['visitingPostalCode']) ? (string) $a['visitingPostalCode'] : (isset($a['postalCode']) ? (string) $a['postalCode'] : null),
            city: isset($a['visitingCity']) ? (string) $a['visitingCity'] : (isset($a['city']) ? (string) $a['city'] : null),
            countryCode: isset($a['countryCode']) ? (string) $a['countryCode'] : null,
            latitude: isset($loc['latitude']) ? (float) $loc['latitude'] : null,
            longitude: isset($loc['longitude']) ? (float) $loc['longitude'] : null,
            openingHoursToday: isset($a['openingHoursToday']) ? (string) $a['openingHoursToday'] : null,
            raw: $a,
        );
    }
}
