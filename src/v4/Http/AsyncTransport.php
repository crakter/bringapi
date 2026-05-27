<?php

declare(strict_types=1);

namespace Bring\Api\Http;

use Bring\Api\Auth\AuthorizationInterface;
use Bring\Api\Endpoint\Endpoint;
use Bring\Api\Exception\BringApiException;
use Bring\Api\Exception\BringTransportException;
use Bring\Api\Exception\InvalidArgumentException;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Same lifecycle as {@see Transport} but returns a Guzzle Promise instead of
 * blocking.
 *
 * Implementation note: Bring has not standardised an async PSR — HTTPlug's
 * async contract exists but is not universally adopted. Guzzle is the
 * de-facto async PSR-18 stack and is a documented suggested dependency for
 * this library, so we accept the coupling and require a Guzzle client here.
 * Callers using a different PSR-18 client can still hit the synchronous
 * {@see Transport}; only the async path is Guzzle-only.
 */
final class AsyncTransport
{
    private LoggerInterface $logger;

    public function __construct(
        private readonly ClientInterface $client,
        private readonly RequestFactoryInterface $requests,
        private readonly StreamFactoryInterface $streams,
        private readonly UriFactoryInterface $uris,
        private readonly AuthorizationInterface $auth,
        ?LoggerInterface $logger = null,
        private readonly bool $testMode = false,
    ) {
        if (!$client instanceof GuzzleClientInterface) {
            throw new InvalidArgumentException(
                'AsyncTransport requires a GuzzleHttp\\ClientInterface (Guzzle is the only mainstream'
                . ' async PSR-18 stack). Install guzzlehttp/guzzle or use the synchronous Transport.',
            );
        }
        if (!class_exists(\GuzzleHttp\Promise\Utils::class)) {
            throw new InvalidArgumentException('AsyncTransport requires guzzlehttp/promises.');
        }
        $this->logger = $logger ?? new NullLogger();
    }

    public function withTestMode(bool $enabled = true): self
    {
        return new self(
            $this->client,
            $this->requests,
            $this->streams,
            $this->uris,
            $this->auth,
            $this->logger,
            $enabled,
        );
    }

    /**
     * Resolves to whatever {@see Endpoint::parseResponse()} returns
     * (typed differently per endpoint).
     *
     * @param Endpoint<mixed> $endpoint
     */
    public function send(Endpoint $endpoint): PromiseInterface
    {
        $request = $endpoint->buildRequest($this->requests, $this->streams, $this->uris);
        $request = $this->auth->applyTo($request);
        if ($this->testMode) {
            $request = $request->withHeader(HeaderNames::TEST_MODE, 'true');
        }

        $this->logger->debug('Bring API async request', [
            'method' => $request->getMethod(),
            'uri' => (string) $request->getUri(),
        ]);

        /** @var GuzzleClientInterface $guzzle */
        $guzzle = $this->client;

        return $guzzle->sendAsync($request)
            ->then(
                function (ResponseInterface $response) use ($endpoint) {
                    if ($response->getStatusCode() >= 400) {
                        throw BringApiException::fromResponse($response);
                    }

                    return $endpoint->parseResponse($response);
                },
                static function (\Throwable $e): void {
                    if ($e instanceof BringApiException) {
                        throw $e;
                    }
                    // Guzzle's default http_errors=true rejects with a BadResponseException
                    // on non-2xx — surface those as BringApiException, not as transport errors.
                    if ($e instanceof \GuzzleHttp\Exception\BadResponseException && $e->getResponse() !== null) {
                        throw BringApiException::fromResponse($e->getResponse(), $e);
                    }
                    throw new BringTransportException(
                        sprintf('Bring API async transport error: %s', $e->getMessage()),
                        0,
                        $e,
                    );
                },
            );
    }

    /**
     * Fan out N endpoints concurrently. Returns a single promise that resolves
     * to a list of results in input order. Rejections short-circuit (use
     * {@see settleAll()} for keep-going semantics).
     *
     * @param array<int|string, Endpoint<mixed>> $endpoints
     */
    public function all(array $endpoints): PromiseInterface
    {
        $promises = [];
        foreach ($endpoints as $key => $endpoint) {
            $promises[$key] = $this->send($endpoint);
        }

        return \GuzzleHttp\Promise\Utils::all($promises);
    }

    /**
     * Fan out N endpoints concurrently, settling every promise. The resolved
     * value mirrors guzzlehttp/promises Utils::settle output:
     *   ['state' => 'fulfilled'|'rejected', 'value'|'reason' => ...]
     *
     * @param array<int|string, Endpoint<mixed>> $endpoints
     */
    public function settleAll(array $endpoints): PromiseInterface
    {
        $promises = [];
        foreach ($endpoints as $key => $endpoint) {
            $promises[$key] = $this->send($endpoint);
        }

        return \GuzzleHttp\Promise\Utils::settle($promises);
    }
}
