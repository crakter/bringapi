<?php

declare(strict_types=1);

namespace Bring\Api\Dto;

final class Dimensions
{
    public function __construct(
        public readonly int $lengthInCm,
        public readonly int $widthInCm,
        public readonly int $heightInCm,
    ) {
    }

    /** @return array<string, int> */
    public function toArray(): array
    {
        return [
            'lengthInCm' => $this->lengthInCm,
            'widthInCm' => $this->widthInCm,
            'heightInCm' => $this->heightInCm,
        ];
    }
}
