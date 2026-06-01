<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Booking;

use Bring\Api\Dto\Address;
use Bring\Api\Exception\InvalidArgumentException;

final class PickupRequest
{
    public function __construct(
        public readonly string $customerNumber,
        public readonly Address $pickupAddress,
        public readonly \DateTimeInterface $readyAt,
        public readonly \DateTimeInterface $closingAt,
        public readonly int $numberOfPackages,
        public readonly int $totalWeightInKg,
        public readonly ?string $additionalInformation = null,
        public readonly bool $testIndicator = false,
    ) {
        if ($customerNumber === '') {
            throw new InvalidArgumentException('PickupRequest: customerNumber must not be empty.');
        }
        if ($numberOfPackages < 1) {
            throw new InvalidArgumentException('PickupRequest: numberOfPackages must be >= 1.');
        }
        if ($closingAt < $readyAt) {
            throw new InvalidArgumentException('PickupRequest: closingAt must be >= readyAt.');
        }
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'customerNumber' => $this->customerNumber,
            'pickupAddress' => $this->pickupAddress->toArray(),
            'readyAt' => $this->readyAt->format(\DateTimeInterface::ATOM),
            'closingAt' => $this->closingAt->format(\DateTimeInterface::ATOM),
            'parcelsInformation' => [
                'numberOfParcels' => $this->numberOfPackages,
                'totalWeightInKg' => $this->totalWeightInKg,
            ],
            'additionalInformation' => $this->additionalInformation,
            'testIndicator' => $this->testIndicator,
        ];
    }
}
