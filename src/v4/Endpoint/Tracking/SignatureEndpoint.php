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
 * GET https://www.mybring.com/tracking/{signatureLinkPath} — returns the
 * raw PNG bytes of a delivery signature.
 *
 * Bring's tracking API surfaces the signature as a *relative path* on each
 * delivery event (see {@see TrackedEvent::$signatureLink}); the path
 * already contains the query string with the package id and timestamp.
 * Example:
 *
 *   $signaturePath = $tracking->latestEvent()->signatureLink;
 *   // → 'api/signatur.png?kollinummer=370123456789&dateTimeIso=2026-…'
 *   $bytes = $bring->tracking()->signature($signaturePath);
 *   file_put_contents('signature.png', $bytes);
 *
 * @implements Endpoint<string>
 */
final class SignatureEndpoint implements Endpoint
{
    private const BASE = 'https://www.mybring.com/tracking/';

    public function __construct(private readonly string $signatureLinkPath)
    {
        if ($signatureLinkPath === '') {
            throw new \Bring\Api\Exception\InvalidArgumentException(
                'SignatureEndpoint: signatureLinkPath must not be empty. '
                . 'Read the path from TrackedEvent::$signatureLink.',
            );
        }
    }

    #[\Override]
    public function method(): HttpMethod
    {
        return HttpMethod::GET;
    }

    #[\Override]
    public function accept(): AcceptType
    {
        return AcceptType::PNG;
    }

    /**
     * Full URL — useful when you want to render the signature in an
     * already-authenticated UI via `<img src="…">` instead of fetching
     * the bytes in PHP.
     */
    public function url(): string
    {
        // Strip any leading slash so we always end up with exactly one
        // separator, regardless of whether Bring's response includes it.
        return self::BASE . ltrim($this->signatureLinkPath, '/');
    }

    #[\Override]
    public function uri(UriFactoryInterface $uris): UriInterface
    {
        return $uris->createUri($this->url());
    }

    #[\Override]
    public function buildRequest(
        RequestFactoryInterface $requests,
        StreamFactoryInterface $streams,
        UriFactoryInterface $uris,
    ): RequestInterface {
        return $requests
            ->createRequest($this->method()->value, $this->uri($uris))
            ->withHeader('Accept', $this->accept()->value);
    }

    #[\Override]
    public function parseResponse(ResponseInterface $response): string
    {
        return (string) $response->getBody();
    }
}
