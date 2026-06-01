<?php

declare(strict_types=1);

namespace Bring\Api\Dto;

final class Package
{
    public function __construct(
        public readonly int $weightInKg,
        public readonly ?Dimensions $dimensions = null,
        public readonly ?string $goodsDescription = null,
        public readonly ?string $correlationId = null,
    ) {
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $a = ['weightInKg' => $this->weightInKg];
        if ($this->dimensions !== null) {
            $a['dimensions'] = $this->dimensions->toArray();
        }
        if ($this->goodsDescription !== null) {
            $a['goodsDescription'] = $this->goodsDescription;
        }
        if ($this->correlationId !== null) {
            $a['correlationId'] = $this->correlationId;
        }

        return $a;
    }
}
