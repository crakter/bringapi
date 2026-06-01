<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Tracking;

final class TrackedPackage
{
    public function __construct(
        public readonly string $packageNumber,
        public readonly ?string $statusDescription,
        /** @var list<TrackedEvent> */
        public readonly array $events,
        /** @var array<mixed, mixed> */
        public readonly array $raw,
    ) {
    }

    /** @param array<mixed, mixed> $a */
    public static function fromArray(array $a): self
    {
        $events = [];
        $set = $a['eventSet'] ?? $a['events'] ?? [];
        if (is_array($set)) {
            foreach ($set as $e) {
                if (is_array($e)) {
                    $events[] = TrackedEvent::fromArray($e);
                }
            }
        }

        return new self(
            packageNumber: (string) ($a['packageNumber'] ?? $a['packageId'] ?? ''),
            statusDescription: isset($a['statusDescription']) ? (string) $a['statusDescription'] : null,
            events: $events,
            raw: $a,
        );
    }
}
