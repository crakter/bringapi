<?php

declare(strict_types=1);

namespace Bring\Api\Http;

use Bring\Api\Auth\AuthorizationInterface;
use Bring\Api\Endpoint\Endpoint;
use Bring\Api\Exception\BringApiException;
use Bring\Api\Exception\BringTransportException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Executes Endpoint objects: build → auth → send → log → parse.
 */
final class Transport
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
     * @template T
     * @param Endpoint<T> $endpoint
     * @return T
     */
    public function send(Endpoint $endpoint): mixed
    {
        $request = $endpoint->buildRequest($this->requests, $this->streams, $this->uris);
        $request = $this->auth->applyTo($request);
        if ($this->testMode) {
            $request = $request->withHeader(HeaderNames::TEST_MODE, 'true');
        }

        $this->logger->debug('Bring API request', [
            'method' => $request->getMethod(),
            'uri' => (string) $request->getUri(),
            'headers' => $this->normaliseHeaders($request->getHeaders()),
        ]);

        try {
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            // A user-supplied Guzzle client with the default http_errors=true
            // rejects 4xx/5xx responses as a BadResponseException — which
            // implements ClientExceptionInterface. We carry a real Bring
            // response here, so surface it as a BringApiException instead
            // of mislabeling it as a transport failure.
            if ($e instanceof \GuzzleHttp\Exception\BadResponseException) {
                throw BringApiException::fromResponse($e->getResponse(), $e);
            }
            throw new BringTransportException(
                sprintf('Bring API transport error: %s', $e->getMessage()),
                0,
                $e,
            );
        }

        $status = $response->getStatusCode();
        $this->logger->debug('Bring API response', [
            'status' => $status,
            'headers' => $this->normaliseHeaders($response->getHeaders()),
        ]);

        if ($status >= 400) {
            throw BringApiException::fromResponse($response);
        }

        return $endpoint->parseResponse($response);
    }

    /**
     * @param array<string, array<int, string>> $headers
     * @return array<string, string>
     */
    private function normaliseHeaders(array $headers): array
    {
        $out = [];
        foreach ($headers as $name => $values) {
            $out[$name] = implode(', ', $values);
        }

        return $out;
    }
}
