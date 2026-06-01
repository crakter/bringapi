<?php

declare(strict_types=1);

namespace Bring\Api\Retry;

/**
 * Test double — records sleeps without blocking the test runner.
 */
final class RecordingSleeper implements Sleeper
{
    /** @var list<float> */
    public array $slept = [];

    #[\Override]
    public function sleepSeconds(float $seconds): void
    {
        $this->slept[] = $seconds;
    }
}
