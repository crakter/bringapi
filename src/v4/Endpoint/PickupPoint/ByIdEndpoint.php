<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\PickupPoint;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Enum\Country;
use Bring\Api\Http\HttpMethod;

/**
 * GET https://api.bring.com/pickuppoint/api/pickuppoint/{country}/id/{id}.json
 *
 * @extends AbstractJsonEndpoint<PickupPointResponse>
 */
final class ByIdEndpoint extends AbstractJsonEndpoint
{
    public function __construct(
        private readonly Country $country,
        private readonly string $id,
    ) {
    }

    public function method(): HttpMethod
    {
        return HttpMethod::GET;
    }

    protected function baseUri(): string
    {
        return sprintf(
            'https://api.bring.com/pickuppoint/api/pickuppoint/%s/id/%s.json',
            $this->country->value,
            rawurlencode($this->id),
        );
    }

    /** @param array<mixed, mixed> $decoded */
    protected function parseDecoded(array $decoded): PickupPointResponse
    {
        return PickupPointResponse::fromArray($decoded);
    }
}
