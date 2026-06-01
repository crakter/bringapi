<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint;

use Bring\Api\Http\AcceptType;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

/**
 * Convenience base for the common case: JSON-out, JSON-in, no body or a JSON body.
 *
 * Subclasses override:
 *   - {@see baseUri()} or {@see uri()}
 *   - {@see method()}
 *   - {@see queryParameters()} for ?a=b&c=d
 *   - {@see jsonBody()} for POST/PUT payloads (return null for no body)
 *   - {@see parseDecoded()} to turn the decoded JSON array into a typed DTO
 *
 * @template TResponse
 * @implements Endpoint<TResponse>
 */
abstract class AbstractJsonEndpoint implements Endpoint
{
    #[\Override]
    public function accept(): AcceptType
    {
        return AcceptType::JSON;
    }

    abstract protected function baseUri(): string;

    #[\Override]
    public function uri(UriFactoryInterface $uris): UriInterface
    {
        $uri = $uris->createUri($this->baseUri());
        $query = http_build_query($this->queryParameters(), '', '&', PHP_QUERY_RFC3986);
        // Bring expects bare repeated keys (additional=A&additional=B). Strip numeric subscripts.
        $query = preg_replace('/%5B(?:\d|[1-9]\d+)%5D=/', '=', $query) ?? $query;

        return $query === '' ? $uri : $uri->withQuery($query);
    }

    /** @return array<string, mixed> */
    protected function queryParameters(): array
    {
        return [];
    }

    /** @return array<mixed, mixed>|null */
    protected function jsonBody(): ?array
    {
        return null;
    }

    /** @return array<string, string> */
    protected function extraHeaders(): array
    {
        return [];
    }

    #[\Override]
    public function buildRequest(
        RequestFactoryInterface $requests,
        StreamFactoryInterface $streams,
        UriFactoryInterface $uris,
    ): RequestInterface {
        $request = $requests
            ->createRequest($this->method()->value, $this->uri($uris))
            ->withHeader('Accept', $this->accept()->value);

        foreach ($this->extraHeaders() as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        $body = $this->jsonBody();
        if ($body !== null) {
            $encoded = json_encode($body, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $request = $request
                ->withHeader('Content-Type', AcceptType::JSON->value)
                ->withBody($streams->createStream($encoded));
        }

        return $request;
    }

    /**
     * @param array<mixed, mixed> $decoded
     * @return TResponse
     */
    abstract protected function parseDecoded(array $decoded): mixed;

    #[\Override]
    public function parseResponse(ResponseInterface $response): mixed
    {
        $body = (string) $response->getBody();
        if ($body === '') {
            return $this->parseDecoded([]);
        }
        $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($decoded)) {
            return $this->parseDecoded([]);
        }

        return $this->parseDecoded($decoded);
    }
}
