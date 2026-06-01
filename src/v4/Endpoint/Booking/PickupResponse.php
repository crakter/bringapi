<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Booking;

final class PickupResponse
{
    public function __construct(
        public readonly ?string $confirmationNumber,
        /** @var array<mixed, mixed> */
        public readonly array $raw,
    ) {
    }

    /** @param array<mixed, mixed> $decoded */
    public static function fromArray(array $decoded): self
    {
        $cn = $decoded['confirmationNumber']
            ?? $decoded['pickupOrderConfirmationNumber']
            ?? $decoded['confirmation']['pickupOrderId']
            ?? null;

        return new self($cn !== null ? (string) $cn : null, $decoded);
    }
}
