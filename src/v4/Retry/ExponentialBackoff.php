<?php

declare(strict_types=1);

namespace Bring\Api\Retry;

/**
 * Standard exponential backoff with full jitter (AWS recommendation):
 *   delay = random(0, min(maxDelay, baseDelay * 2^(attempt - 1)))
 *
 * Defaults to 0.5s base / 30s ceiling — works for Bring's documented
 * 80 req/s Pickup Point limit and 20 concurrent Reports limit.
 */
final class ExponentialBackoff implements BackoffStrategy
{
    /** @var callable(): float */
    private $rng;

    public function __construct(
        private readonly float $baseDelaySeconds = 0.5,
        private readonly float $maxDelaySeconds = 30.0,
        ?callable $rng = null,
    ) {
        if ($baseDelaySeconds < 0 || $maxDelaySeconds < $baseDelaySeconds) {
            throw new \InvalidArgumentException('ExponentialBackoff: invalid delay bounds.');
        }
        $this->rng = $rng ?? static fn (): float => mt_rand() / mt_getrandmax();
    }

    #[\Override]
    public function delaySeconds(int $attempt): float
    {
        if ($attempt < 1) {
            return 0.0;
        }
        $exp = min($this->maxDelaySeconds, $this->baseDelaySeconds * (float) (2 ** ($attempt - 1)));

        return ($this->rng)() * $exp;
    }
}
