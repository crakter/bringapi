<?php

declare(strict_types=1);

namespace Bring\Api\Exception;

/** A single error item parsed from a Bring error response. */
final class BringApiError
{
    public function __construct(
        public readonly string $code,
        public readonly string $message,
        public readonly ?string $type = null,
    ) {
    }
}
