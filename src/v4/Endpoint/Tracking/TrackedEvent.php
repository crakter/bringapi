<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Tracking;

final class TrackedEvent
{
    public function __construct(
        public readonly ?\DateTimeImmutable $dateIso,
        public readonly string $description,
        public readonly ?string $status,
        public readonly ?string $city,
        public readonly ?string $countryCode,
        /** @var array<mixed, mixed> */
        public readonly array $raw,
    ) {
    }

    /** @param array<mixed, mixed> $a */
    public static function fromArray(array $a): self
    {
        $iso = null;
        if (isset($a['dateIso']) && is_string($a['dateIso'])) {
            try {
                $iso = new \DateTimeImmutable($a['dateIso']);
            } catch (\Exception) {
                $iso = null;
            }
        }

        return new self(
            dateIso: $iso,
            description: (string) ($a['description'] ?? ''),
            status: isset($a['status']) ? (string) $a['status'] : null,
            city: isset($a['city']) ? (string) $a['city'] : null,
            countryCode: isset($a['countryCode']) ? (string) $a['countryCode'] : null,
            raw: $a,
        );
    }
}
