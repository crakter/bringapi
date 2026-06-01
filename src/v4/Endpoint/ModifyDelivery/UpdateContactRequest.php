<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\ModifyDelivery;

use Bring\Api\Dto\Contact;
use Bring\Api\Exception\InvalidArgumentException;

final class UpdateContactRequest
{
    public function __construct(
        public readonly string $consignmentNumber,
        public readonly Contact $contact,
    ) {
        if ($consignmentNumber === '') {
            throw new InvalidArgumentException('UpdateContactRequest: consignmentNumber must not be empty.');
        }
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'consignmentNumber' => $this->consignmentNumber,
            'contact' => $this->contact->toArray(),
        ];
    }
}
