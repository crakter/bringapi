<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\PickupPoint;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Enum\Country;
use Bring\Api\Http\HttpMethod;

/**
 * GET https://api.bring.com/pickuppoint/api/pickuppoint/{country}/postalCode/{postalCode}.json
 *
 * @extends AbstractJsonEndpoint<PickupPointListResponse>
 */
final class ByPostalCodeEndpoint extends AbstractJsonEndpoint
{
    public function __construct(
        private readonly Country $country,
        private readonly string $postalCode,
    ) {
    }

    public function method(): HttpMethod
    {
        return HttpMethod::GET;
    }

    protected function baseUri(): string
    {
        return sprintf(
            'https://api.bring.com/pickuppoint/api/pickuppoint/%s/postalCode/%s.json',
            $this->country->value,
            rawurlencode($this->postalCode),
        );
    }

    /** @param array<mixed, mixed> $decoded */
    protected function parseDecoded(array $decoded): PickupPointListResponse
    {
        return PickupPointListResponse::fromArray($decoded);
    }
}
