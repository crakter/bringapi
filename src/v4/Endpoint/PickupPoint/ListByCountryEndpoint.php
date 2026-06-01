<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\PickupPoint;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Enum\Country;
use Bring\Api\Http\HttpMethod;

/**
 * GET https://api.bring.com/pickuppoint/api/pickuppoint/{country}/all.json
 *
 * @extends AbstractJsonEndpoint<PickupPointListResponse>
 */
final class ListByCountryEndpoint extends AbstractJsonEndpoint
{
    public function __construct(private readonly Country $country)
    {
    }

    #[\Override]
    public function method(): HttpMethod
    {
        return HttpMethod::GET;
    }

    #[\Override]
    protected function baseUri(): string
    {
        return sprintf(
            'https://api.bring.com/pickuppoint/api/pickuppoint/%s/all.json',
            $this->country->value,
        );
    }

    /** @param array<mixed, mixed> $decoded */
    #[\Override]
    protected function parseDecoded(array $decoded): PickupPointListResponse
    {
        return PickupPointListResponse::fromArray($decoded);
    }
}
