<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Shipping;

use Bring\Api\Http\Transport;

final class ShippingGuideApi
{
    public function __construct(private readonly Transport $transport)
    {
    }

    /** @return PriceResponse */
    public function price(PriceRequest $request): PriceResponse
    {
        return $this->transport->send(new PriceEndpoint($request));
    }

    /** @return DeliveryTimeResponse */
    public function deliveryTime(PriceRequest $request): DeliveryTimeResponse
    {
        return $this->transport->send(new DeliveryTimeEndpoint($request));
    }

    /** @return ProductsResponse */
    public function products(PriceRequest $request): ProductsResponse
    {
        return $this->transport->send(new ProductsEndpoint($request));
    }
}
