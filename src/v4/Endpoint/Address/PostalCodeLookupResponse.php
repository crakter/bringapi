<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Address;

final class PostalCodeLookupResponse
{
    public function __construct(
        public readonly string $postalCode,
        public readonly string $city,
        public readonly ?string $municipality,
        public readonly ?string $county,
        public readonly ?string $postalCodeType,
        /** @var array<mixed, mixed> */
        public readonly array $raw,
    ) {
    }

    /** @param array<mixed, mixed> $decoded */
    public static function fromArray(array $decoded): self
    {
        return new self(
            postalCode: (string) ($decoded['postal_code'] ?? $decoded['postalCode'] ?? ''),
            city: (string) ($decoded['city'] ?? ''),
            municipality: isset($decoded['municipality']) ? (string) $decoded['municipality'] : null,
            county: isset($decoded['county']) ? (string) $decoded['county'] : null,
            // Bring deprecated po_box_only in Aug 2025 in favour of postal_code_type.
            postalCodeType: isset($decoded['postal_code_type']) ? (string) $decoded['postal_code_type'] : null,
            raw: $decoded,
        );
    }
}
