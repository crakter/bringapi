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

    #[\Override]
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $attempt = 0;
        $lastException = null;
        $lastResponse = null;
        $response = null;

        while ($attempt < $this->maxAttempts) {
            $attempt++;
            // POST bodies (BookEndpoint, ChangeAddressEndpoint, etc.) are
            // already consumed after the first sendRequest() — Guzzle and
            // most PSR-18 clients read the stream to EOF. Rewind before
            // retrying or we'll send an empty body the second time.
            if ($attempt > 1) {
                $body = $request->getBody();
                if ($body->isSeekable()) {
                    $body->rewind();
                }
            }
            try {
                $response = $this->inner->sendRequest($request);
            } catch (ClientExceptionInterface $e) {
                // A Guzzle client with the default http_errors=true throws
                // BadResponseException for every non-2xx — including
                // non-retryable 4xx (400, 401, 403, 404). Extract the
                // response and treat it the same as if the inner client
                // had returned it normally; otherwise we'd burn retry
                // attempts on permanent failures.
                if ($e instanceof \GuzzleHttp\Exception\BadResponseException) {
                    $response = $e->getResponse();
                    if (!in_array($response->getStatusCode(), $this->retryStatuses, true)) {
                        throw $e;
                    }
                    $lastResponse = $response;
                    $lastException = $e;
                    if ($attempt >= $this->maxAttempts) {
                        throw $e;
                    }
                    $delay = $this->retryAfter($response) ?? $this->backoff->delaySeconds($attempt);
                    $this->logger->info('Bring API retrying after HTTP error', [
                        'attempt' => $attempt,
                        'status' => $response->getStatusCode(),
                        'delay' => $delay,
                    ]);
                    $this->sleeper->sleepSeconds($delay);
                    continue;
                }
                $lastException = $e;
                $lastResponse = null;
                $response = null;
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
        // gives a clean message if the loop logic regresses.
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
