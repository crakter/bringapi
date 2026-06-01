<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Address;

final class MailboxDeliveryDatesResponse
{
    /**
     * @param list<\DateTimeImmutable> $deliveryDates
     * @param array<mixed, mixed>      $raw
     */
    public function __construct(
        public readonly array $deliveryDates,
        public readonly array $raw,
    ) {
    }

    /** @param array<mixed, mixed> $decoded */
    public static function fromArray(array $decoded): self
    {
        $dates = [];
        $raw = $decoded['delivery_dates'] ?? $decoded['deliveryDates'] ?? [];
        if (is_array($raw)) {
            foreach ($raw as $d) {
                if (!is_string($d)) {
                    continue;
                }
                try {
                    $dates[] = new \DateTimeImmutable($d);
                } catch (\Exception) {
                    continue;
                }
            }
        }

        return new self($dates, $decoded);
    }
}
