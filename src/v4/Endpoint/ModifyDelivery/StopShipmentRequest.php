<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\ModifyDelivery;

use Bring\Api\Exception\InvalidArgumentException;

final class StopShipmentRequest
{
    public function __construct(
        public readonly string $consignmentNumber,
        public readonly string $reason = '',
    ) {
        if ($consignmentNumber === '') {
            throw new InvalidArgumentException('StopShipmentRequest: consignmentNumber must not be empty.');
        }
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $a = ['consignmentNumber' => $this->consignmentNumber];
        if ($this->reason !== '') {
            $a['reason'] = $this->reason;
        }

        return $a;
    }
}
