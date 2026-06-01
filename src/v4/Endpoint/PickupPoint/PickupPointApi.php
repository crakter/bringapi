<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\PickupPoint;

use Bring\Api\Enum\Country;
use Bring\Api\Exception\InvalidArgumentException;
use Bring\Api\Http\Transport;

/**
 * Pickup Point API — https://developer.bring.com/api/pickup-point/.
 *
 * Bring only serves Norway, Sweden, Denmark and Finland for this endpoint.
 * Rate-limited to 80 req/s.
 */
final class PickupPointApi
{
    private const SUPPORTED = [Country::NO, Country::SE, Country::DK, Country::FI];

    public function __construct(private readonly Transport $transport)
    {
    }

    public function all(Country $country): PickupPointListResponse
    {
        $this->assertSupported($country);

        return $this->transport->send(new ListByCountryEndpoint($country));
    }

    public function byId(Country $country, string $pickupPointId): PickupPointResponse
    {
        $this->assertSupported($country);

        return $this->transport->send(new ByIdEndpoint($country, $pickupPointId));
    }

    public function byPostalCode(Country $country, string $postalCode): PickupPointListResponse
    {
        $this->assertSupported($country);

        return $this->transport->send(new ByPostalCodeEndpoint($country, $postalCode));
    }

    public function byLocation(Country $country, float $latitude, float $longitude): PickupPointListResponse
    {
        $this->assertSupported($country);

        return $this->transport->send(new ByLocationEndpoint($country, $latitude, $longitude));
    }

    private function assertSupported(Country $country): void
    {
        if (!in_array($country, self::SUPPORTED, true)) {
            throw new InvalidArgumentException(sprintf(
                'PickupPointApi: country %s is not supported. Bring serves only %s.',
                $country->value,
                implode(', ', array_map(static fn (Country $c): string => $c->value, self::SUPPORTED)),
            ));
        }
    }
}
