<?php

declare(strict_types=1);

namespace Bring\Api\Http;

use Bring\Api\Retry\BackoffStrategy;
use Bring\Api\Retry\ExponentialBackoff;
use Bring\Api\Retry\Sleeper;
use Bring\Api\Retry\SystemSleeper;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * PSR-18 decorator that retries transient failures with exponential backoff.
 *
 * Retries on:
 *   - any PSR-18 ClientExceptionInterface (DNS / TLS / connect timeout)
 *   - HTTP 429 (Too Many Requests) — honours Retry-After
 *   - HTTP 502/503/504
 *
 * Idempotency: retries every request regardless of method. Bring's POST
 * endpoints (booking, pickup, modify-delivery) are guarded server-side by
 * correlationId / consignment dedup; the alternative — failing fast on
 * transient 503s during a booking — is worse than a duplicate write that
 * Bring rejects with a clear error code.
 */
final class RetryClient implements ClientInterface
{
    /** HTTP statuses that should trigger another attempt. */
    public const DEFAULT_RETRY_STATUSES = [408, 425, 429, 500, 502, 503, 504];

    /** @param list<int> $retryStatuses */
    public function __construct(
        private readonly ClientInterface $inner,
        private readonly int $maxAttempts = 4,
        private readonly array $retryStatuses = self::DEFAULT_RETRY_STATUSES,
        private readonly BackoffStrategy $backoff = new ExponentialBackoff(),
        private readonly Sleeper $sleeper = new SystemSleeper(),
        ?LoggerInterface $logger = null,
    ) {
        if ($maxAttempts < 1) {
            throw new \InvalidArgumentException('RetryClient: maxAttempts must be >= 1.');
        }
        $this->logger = $logger ?? new NullLogger();
    }

    private LoggerInterface $logger;

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $attempt = 0;
        $lastException = null;
        $lastResponse = null;

        while ($attempt < $this->maxAttempts) {
            $attempt++;
            try {
                $response = $this->inner->sendRequest($request);
            } catch (ClientExceptionInterface $e) {
                $lastException = $e;
                $lastResponse = null;
                if ($attempt >= $this->maxAttempts) {
                    throw $e;
                }
                $this->sleeper->sleepSeconds($this->backoff->delaySeconds($attempt));
                $this->logger->info('Bring API retrying after transport error', [
                    'attempt' => $attempt,
                    'error' => $e->getMessage(),
                ]);
                continue;
            }

            if (!in_array($response->getStatusCode(), $this->retryStatuses, true)) {
                return $response;
            }

            $lastResponse = $response;
            if ($attempt >= $this->maxAttempts) {
                return $response;
            }

            $delay = $this->retryAfter($response) ?? $this->backoff->delaySeconds($attempt);
            $this->logger->info('Bring API retrying after HTTP error', [
                'attempt' => $attempt,
                'status' => $response->getStatusCode(),
                'delay' => $delay,
            ]);
            $this->sleeper->sleepSeconds($delay);
        }

        // Unreachable in practice — every branch above either returns or throws — but
        // makes the compiler happy and gives a clean message if logic regresses.
        if ($lastResponse !== null) {
            return $lastResponse;
        }
        throw $lastException ?? new \RuntimeException('RetryClient: exhausted retries with no response.');
    }

    /**
     * Parse a Retry-After header into seconds. Bring's docs use the seconds
     * form; the HTTP-date form is supported for completeness.
     */
    private function retryAfter(ResponseInterface $response): ?float
    {
        $header = $response->getHeaderLine('Retry-After');
        if ($header === '') {
            return null;
        }
        if (ctype_digit(trim($header))) {
            return (float) $header;
        }
        $ts = strtotime($header);
        if ($ts === false) {
            return null;
        }
        $delta = $ts - time();

        return $delta > 0 ? (float) $delta : 0.0;
    }
}
