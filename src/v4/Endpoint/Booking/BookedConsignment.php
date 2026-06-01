<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Booking;

final class BookedConsignment
{
    public function __construct(
        public readonly ?string $confirmation,
        public readonly ?string $labelUrl,
        public readonly ?string $pdfUrl,
        /** @var list<string> */
        public readonly array $packageNumbers,
        /** @var array<mixed, mixed> */
        public readonly array $raw,
    ) {
    }

    /** @param array<mixed, mixed> $a */
    public static function fromArray(array $a): self
    {
        $confirmation = $a['confirmation']['consignmentNumber']
            ?? $a['confirmation']['shipmentNumber']
            ?? $a['confirmation']['bookingNumber']
            ?? null;

        $packageNumbers = [];
        if (isset($a['confirmation']['packages']) && is_array($a['confirmation']['packages'])) {
            foreach ($a['confirmation']['packages'] as $p) {
                if (is_array($p) && isset($p['packageNumber'])) {
                    $packageNumbers[] = (string) $p['packageNumber'];
                }
            }
        }

        return new self(
            confirmation: $confirmation !== null ? (string) $confirmation : null,
            labelUrl: $a['confirmation']['links']['label']
                ?? $a['confirmation']['labels']['label']
                ?? null,
            pdfUrl: $a['confirmation']['links']['labels']
                ?? $a['confirmation']['labels']['pdf']
                ?? null,
            packageNumbers: $packageNumbers,
            raw: $a,
        );
    }
}
