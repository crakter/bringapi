<?php

declare(strict_types=1);

namespace Bring\Api\Retry;

final class SystemSleeper implements Sleeper
{
    public function sleepSeconds(float $seconds): void
    {
        if ($seconds <= 0.0) {
            return;
        }
        usleep((int) round($seconds * 1_000_000));
    }
}
