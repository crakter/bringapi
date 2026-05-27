<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Tracking;

use Bring\Api\Endpoint\Endpoint;
use Bring\Api\Http\AcceptType;
use Bring\Api\Http\HttpMethod;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

/**
 * GET https://www.mybring.com/tracking/{id} (returns image bytes)
 *
 * @implements Endpoint<string>
 */
final class SignatureEndpoint implements Endpoint
{
    public function __construct(private readonly string $trackingId)
    {
    }

    public function method(): HttpMethod
    {
        return HttpMethod::GET;
    }

    public function accept(): AcceptType
    {
        return AcceptType::PNG;
    }

    public function uri(UriFactoryInterface $uris): UriInterface
    {
        return $uris->createUri(sprintf(
            'https://www.mybring.com/tracking/%s',
            rawurlencode($this->trackingId),
        ));
    }

    public function buildRequest(
        RequestFactoryInterface $requests,
        StreamFactoryInterface $streams,
        UriFactoryInterface $uris,
    ): RequestInterface {
        return $requests
            ->createRequest($this->method()->value, $this->uri($uris))
            ->withHeader('Accept', $this->accept()->value);
    }

    public function parseResponse(ResponseInterface $response): string
    {
        return (string) $response->getBody();
    }
}
