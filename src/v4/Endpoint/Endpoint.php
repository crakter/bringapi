<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint;

use Bring\Api\Http\AcceptType;
use Bring\Api\Http\HttpMethod;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

/**
 * One Bring endpoint call. Builds the PSR-7 request and parses the response.
 *
 * @template TResponse
 */
interface Endpoint
{
    public function method(): HttpMethod;

    public function uri(UriFactoryInterface $uris): \Psr\Http\Message\UriInterface;

    public function accept(): AcceptType;

    public function buildRequest(
        RequestFactoryInterface $requests,
        StreamFactoryInterface $streams,
        UriFactoryInterface $uris,
    ): RequestInterface;

    /**
     * @return TResponse
     */
    public function parseResponse(ResponseInterface $response): mixed;
}
