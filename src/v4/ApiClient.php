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
use Bring\Api\Http\ClientFactory;
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
    ) {
    }

    public static function withCredentials(
        Credentials $credentials,
        ?ClientInterface $httpClient = null,
        ?LoggerInterface $logger = null,
    ): self {
        return self::build(new HeaderAuthorization($credentials), $httpClient, self::wrapLogger($logger, $credentials));
    }

    public static function withoutAuth(
        ?ClientInterface $httpClient = null,
        ?LoggerInterface $logger = null,
    ): self {
        return self::build(new NullAuthorization(), $httpClient, $logger);
    }

    public static function build(
        AuthorizationInterface $auth,
        ?ClientInterface $httpClient = null,
        ?LoggerInterface $logger = null,
        ?RequestFactoryInterface $requests = null,
        ?StreamFactoryInterface $streams = null,
        ?UriFactoryInterface $uris = null,
    ): self {
        return new self(new Transport(
            $httpClient ?? ClientFactory::defaultClient(),
            $requests ?? ClientFactory::defaultRequestFactory(),
            $streams ?? ClientFactory::defaultStreamFactory(),
            $uris ?? ClientFactory::defaultUriFactory(),
            $auth,
            $logger,
        ));
    }

    private static function wrapLogger(?LoggerInterface $logger, Credentials $credentials): ?LoggerInterface
    {
        if ($logger === null) {
            return null;
        }
        $wrapped = new RedactingLogger($logger, [$credentials->getApiKey()]);

        return $wrapped;
    }

    public function withTestMode(bool $enabled = true): self
    {
        return new self($this->transport->withTestMode($enabled));
    }

    public function transport(): Transport
    {
        return $this->transport;
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
