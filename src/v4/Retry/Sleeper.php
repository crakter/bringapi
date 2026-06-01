<?php

declare(strict_types=1);

namespace Bring\Api\Retry;

interface Sleeper
{
    public function sleepSeconds(float $seconds): void;
}
