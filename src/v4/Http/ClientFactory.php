<?php

declare(strict_types=1);

namespace Bring\Api\Http;

use Bring\Api\Exception\InvalidArgumentException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

/**
 * Discovers a PSR-18 / PSR-17 stack at runtime. Guzzle is preferred (and a
 * dev dependency), but any PSR-18 client works.
 */
final class ClientFactory
{
    public static function defaultClient(): ClientInterface
    {
        if (class_exists(\GuzzleHttp\Client::class)) {
            return new \GuzzleHttp\Client([
                'http_errors' => false,
                'connect_timeout' => 10,
                'timeout' => 30,
            ]);
        }

        throw new InvalidArgumentException(
            'No PSR-18 HTTP client found. Install guzzlehttp/guzzle or pass your own ClientInterface to ApiClient.',
        );
    }

    public static function defaultRequestFactory(): RequestFactoryInterface
    {
        if (class_exists(\GuzzleHttp\Psr7\HttpFactory::class)) {
            return new \GuzzleHttp\Psr7\HttpFactory();
        }
        if (class_exists(\Http\Factory\Guzzle\RequestFactory::class)) {
            return new \Http\Factory\Guzzle\RequestFactory();
        }
        throw new InvalidArgumentException('No PSR-17 RequestFactoryInterface found. Install guzzlehttp/psr7 or pass your own factory.');
    }

    public static function defaultStreamFactory(): StreamFactoryInterface
    {
        if (class_exists(\GuzzleHttp\Psr7\HttpFactory::class)) {
            return new \GuzzleHttp\Psr7\HttpFactory();
        }
        if (class_exists(\Http\Factory\Guzzle\StreamFactory::class)) {
            return new \Http\Factory\Guzzle\StreamFactory();
        }
        throw new InvalidArgumentException('No PSR-17 StreamFactoryInterface found. Install guzzlehttp/psr7 or pass your own factory.');
    }

    public static function defaultUriFactory(): UriFactoryInterface
    {
        if (class_exists(\GuzzleHttp\Psr7\HttpFactory::class)) {
            return new \GuzzleHttp\Psr7\HttpFactory();
        }
        if (class_exists(\Http\Factory\Guzzle\UriFactory::class)) {
            return new \Http\Factory\Guzzle\UriFactory();
        }
        throw new InvalidArgumentException('No PSR-17 UriFactoryInterface found. Install guzzlehttp/psr7 or pass your own factory.');
    }
}
