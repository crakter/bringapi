<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Shipping;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Http\HttpMethod;

/**
 * GET https://api.bring.com/shippingguide/v2/products/expectedDelivery
 *
 * @extends AbstractJsonEndpoint<DeliveryTimeResponse>
 */
final class DeliveryTimeEndpoint extends AbstractJsonEndpoint
{
    public function __construct(private readonly PriceRequest $request)
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
        return 'https://api.bring.com/shippingguide/v2/products/expectedDelivery';
    }

    /** @return array<string, mixed> */
    #[\Override]
    protected function queryParameters(): array
    {
        return $this->request->toQuery();
    }

    /** @param array<mixed, mixed> $decoded */
    #[\Override]
    protected function parseDecoded(array $decoded): DeliveryTimeResponse
    {
        return DeliveryTimeResponse::fromArray($decoded);
    }
}
