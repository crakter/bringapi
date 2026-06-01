<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\PostalCode;

final class PostalCodeResponse
{
    public function __construct(
        public readonly bool $valid,
        public readonly ?string $postalCode,
        public readonly ?string $city,
        /** @var array<mixed, mixed> */
        public readonly array $raw,
    ) {
    }

    /** @param array<mixed, mixed> $decoded */
    public static function fromArray(array $decoded): self
    {
        $valid = (bool) ($decoded['valid'] ?? false);

        return new self(
            valid: $valid,
            postalCode: isset($decoded['postal_code']) ? (string) $decoded['postal_code'] : (isset($decoded['postalCode']) ? (string) $decoded['postalCode'] : null),
            city: isset($decoded['result']) ? (string) $decoded['result'] : (isset($decoded['city']) ? (string) $decoded['city'] : null),
            raw: $decoded,
        );
    }
}
