<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\ModifyDelivery;

use Bring\Api\Dto\Address;
use Bring\Api\Exception\InvalidArgumentException;

final class ChangeAddressRequest
{
    public function __construct(
        public readonly string $consignmentNumber,
        public readonly Address $newRecipientAddress,
    ) {
        if ($consignmentNumber === '') {
            throw new InvalidArgumentException('ChangeAddressRequest: consignmentNumber must not be empty.');
        }
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'consignmentNumber' => $this->consignmentNumber,
            'newRecipient' => $this->newRecipientAddress->toArray(),
        ];
    }
}
