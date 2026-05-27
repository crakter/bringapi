<?php

declare(strict_types=1);

namespace Bring\Api\Retry;

interface BackoffStrategy
{
    /** Delay in seconds before attempt number $attempt (1-indexed: 1 = first retry). */
    public function delaySeconds(int $attempt): float;
}
