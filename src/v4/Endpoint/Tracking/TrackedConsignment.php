<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Tracking;

final class TrackedConsignment
{
    public function __construct(
        public readonly string $consignmentId,
        /** @var list<TrackedPackage> */
        public readonly array $packages,
        /** @var array<mixed, mixed> */
        public readonly array $raw,
    ) {
    }

    /** @param array<mixed, mixed> $a */
    public static function fromArray(array $a): self
    {
        $packages = [];
        $set = $a['packageSet'] ?? $a['packages'] ?? [];
        if (is_array($set)) {
            foreach ($set as $p) {
                if (is_array($p)) {
                    $packages[] = TrackedPackage::fromArray($p);
                }
            }
        }

        return new self(
            consignmentId: (string) ($a['consignmentId'] ?? ''),
            packages: $packages,
            raw: $a,
        );
    }
}
