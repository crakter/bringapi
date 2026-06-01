<?php

declare(strict_types=1);

namespace Bring\Api\Dto;

final class Contact
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $email = null,
        public readonly ?string $phoneNumber = null,
        public readonly ?string $mobileNumber = null,
    ) {
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'email' => $this->email,
            'phoneNumber' => $this->phoneNumber,
            'mobileNumber' => $this->mobileNumber,
        ], static fn ($v) => $v !== null);
    }
}
