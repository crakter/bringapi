<?php

declare(strict_types=1);

namespace Bring\Api;

use Bring\Api\Auth\AuthorizationInterface;
use Bring\Api\Auth\Credentials;
use Bring\Api\Auth\HeaderAuthorization;
use Bring\Api\Auth\NullAuthorization;
use Bring\Api\Endpoint\Address\AddressApi;
use Bring\Api\Endpoint\Booking\BookingApi;
use Bring\Api\Endpoint\ModifyDelivery\ModifyDeliveryApi;
use Bring\Api\Endpoint\OrderManagement\OrderManagementApi;
use Bring\Api\Endpoint\PickupPoint\PickupPointApi;
use Bring\Api\Endpoint\PostalCode\PostalCodeApi;
use Bring\Api\Endpoint\Reports\ReportsApi;
use Bring\Api\Endpoint\Shipping\ShippingGuideApi;
use Bring\Api\Endpoint\Tracking\TrackingApi;
use Bring\Api\Http\AsyncTransport;
use Bring\Api\Http\ClientFactory;
use Bring\Api\Http\RetryClient;
use Bring\Api\Http\Transport;
use Bring\Api\Logging\RedactingLogger;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Top-level entry point.
 *
 * <code>
 * $bring = ApiClient::withCredentials(new Credentials('me@example.com', $apiKey, 'https://example.com'));
 * $price = $bring->shippingGuide()->price(new PriceRequest(...));
 * </code>
 */
final class ApiClient
{
    public function __construct(
        private readonly Transport $transport,
        private readonly ClientInterface $httpClient,
        private readonly RequestFactoryInterface $requests,
        private readonly StreamFactoryInterface $streams,
        private readonly UriFactoryInterface $uris,
        private readonly AuthorizationInterface $auth,
        private readonly ?LoggerInterface $logger = null,
        private readonly bool $testMode = false,
    ) {
    }

    public static function withCredentials(
        Credentials $credentials,
        ?ClientInterface $httpClient = null,
        ?LoggerInterface $logger = null,
        bool $retry = true,
    ): self {
        return self::build(
            new HeaderAuthorization($credentials),
            $httpClient,
            self::wrapLogger($logger, $credentials),
            retry: $retry,
        );
    }

    public static function withoutAuth(
        ?ClientInterface $httpClient = null,
        ?LoggerInterface $logger = null,
        bool $retry = true,
    ): self {
        return self::build(new NullAuthorization(), $httpClient, $logger, retry: $retry);
    }

    public static function build(
        AuthorizationInterface $auth,
        ?ClientInterface $httpClient = null,
        ?LoggerInterface $logger = null,
        ?RequestFactoryInterface $requests = null,
        ?StreamFactoryInterface $streams = null,
        ?UriFactoryInterface $uris = null,
        bool $retry = true,
    ): self {
        $client = $httpClient ?? ClientFactory::defaultClient();
        if ($retry && !$client instanceof RetryClient) {
            $client = new RetryClient($client, logger: $logger);
        }
        $requests ??= ClientFactory::defaultRequestFactory();
        $streams ??= ClientFactory::defaultStreamFactory();
        $uris ??= ClientFactory::defaultUriFactory();

        return new self(
            new Transport($client, $requests, $streams, $uris, $auth, $logger),
            $client,
            $requests,
            $streams,
            $uris,
            $auth,
            $logger,
        );
    }

    private static function wrapLogger(?LoggerInterface $logger, Credentials $credentials): ?LoggerInterface
    {
        if ($logger === null) {
            return null;
        }

        return new RedactingLogger($logger, [$credentials->getApiKey()]);
    }

    public function withTestMode(bool $enabled = true): self
    {
        return new self(
            $this->transport->withTestMode($enabled),
            $this->httpClient,
            $this->requests,
            $this->streams,
            $this->uris,
            $this->auth,
            $this->logger,
            $enabled,
        );
    }

    public function transport(): Transport
    {
        return $this->transport;
    }

    /**
     * Returns an async transport that yields Guzzle promises. Throws if the
     * configured HTTP client is not Guzzle.
     */
    public function async(): AsyncTransport
    {
        return new AsyncTransport(
            $this->httpClient,
            $this->requests,
            $this->streams,
            $this->uris,
            $this->auth,
            $this->logger,
            $this->testMode,
        );
    }

    public function shippingGuide(): ShippingGuideApi
    {
        return new ShippingGuideApi($this->transport);
    }

    public function booking(): BookingApi
    {
        return new BookingApi($this->transport);
    }

    public function tracking(): TrackingApi
    {
        return new TrackingApi($this->transport);
    }

    public function reports(): ReportsApi
    {
        return new ReportsApi($this->transport);
    }

    public function postalCode(): PostalCodeApi
    {
        return new PostalCodeApi($this->transport);
    }

    public function address(): AddressApi
    {
        return new AddressApi($this->transport);
    }

    public function pickupPoint(): PickupPointApi
    {
        return new PickupPointApi($this->transport);
    }

    public function modifyDelivery(): ModifyDeliveryApi
    {
        return new ModifyDeliveryApi($this->transport);
    }

    public function orderManagement(): OrderManagementApi
    {
        return new OrderManagementApi($this->transport);
    }
}
