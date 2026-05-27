<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\PickupPoint;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Enum\Country;
use Bring\Api\Http\HttpMethod;

/**
 * GET https://api.bring.com/pickuppoint/api/pickuppoint/{country}/location/{lat},{lng}.json
 *
 * @extends AbstractJsonEndpoint<PickupPointListResponse>
 */
final class ByLocationEndpoint extends AbstractJsonEndpoint
{
    public function __construct(
        private readonly Country $country,
        private readonly float $latitude,
        private readonly float $longitude,
    ) {
    }

    public function method(): HttpMethod
    {
        return HttpMethod::GET;
    }

    protected function baseUri(): string
    {
        return sprintf(
            'https://api.bring.com/pickuppoint/api/pickuppoint/%s/location/%s,%s.json',
            $this->country->value,
            rtrim(rtrim(sprintf('%.6F', $this->latitude), '0'), '.'),
            rtrim(rtrim(sprintf('%.6F', $this->longitude), '0'), '.'),
        );
    }

    /** @param array<mixed, mixed> $decoded */
    protected function parseDecoded(array $decoded): PickupPointListResponse
    {
        return PickupPointListResponse::fromArray($decoded);
    }
}
