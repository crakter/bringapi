<?php

declare(strict_types=1);

namespace Bring\Api\Retry;

final class SystemSleeper implements Sleeper
{
    #[\Override]
    public function sleepSeconds(float $seconds): void
    {
        if ($seconds <= 0.0) {
            return;
        }
        usleep((int) round($seconds * 1_000_000.0));
    }
}
