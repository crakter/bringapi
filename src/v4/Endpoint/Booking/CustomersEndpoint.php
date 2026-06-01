<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Booking;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Http\HttpMethod;

/**
 * GET https://api.bring.com/booking/api/customers.json
 *
 * Bring also exposes an `.xml` variant, but this endpoint extends
 * AbstractJsonEndpoint — XML responses cannot be parsed here.
 *
 * @extends AbstractJsonEndpoint<CustomersResponse>
 */
final class CustomersEndpoint extends AbstractJsonEndpoint
{
    #[\Override]
    public function method(): HttpMethod
    {
        return HttpMethod::GET;
    }

    #[\Override]
    protected function baseUri(): string
    {
        return 'https://api.bring.com/booking/api/customers.json';
    }

    /** @param array<mixed, mixed> $decoded */
    #[\Override]
    protected function parseDecoded(array $decoded): CustomersResponse
    {
        return CustomersResponse::fromArray($decoded);
    }
}
