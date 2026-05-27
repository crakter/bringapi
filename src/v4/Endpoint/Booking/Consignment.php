<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Booking;

use Bring\Api\Dto\Address;
use Bring\Api\Dto\Package;

final class Consignment
{
    /** @param list<Package> $packages */
    public function __construct(
        public readonly BookingProduct $product,
        public readonly Address $sender,
        public readonly Address $recipient,
        public readonly array $packages,
        public readonly ?string $correlationId = null,
        public readonly ?\DateTimeInterface $shippingDateTime = null,
    ) {
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $a = [
            'product' => $this->product->toArray(),
            'parties' => [
                'sender' => $this->sender->toArray(),
                'recipient' => $this->recipient->toArray(),
            ],
            'packages' => array_map(static fn (Package $p): array => $p->toArray(), $this->packages),
        ];
        if ($this->correlationId !== null) {
            $a['correlationId'] = $this->correlationId;
        }
        if ($this->shippingDateTime !== null) {
            $a['shippingDateTime'] = $this->shippingDateTime->format(\DateTimeInterface::ATOM);
        }

        return $a;
    }
}
