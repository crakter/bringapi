<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Shipping;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Http\HttpMethod;

/**
 * GET https://api.bring.com/shippingguide/v2/products
 *
 * Returns full product details — price + delivery time + GUI info when the
 * matching flags are set on the request.
 *
 * @extends AbstractJsonEndpoint<ProductsResponse>
 */
final class ProductsEndpoint extends AbstractJsonEndpoint
{
    public function __construct(private readonly PriceRequest $request)
    {
    }

    public function method(): HttpMethod
    {
        return HttpMethod::GET;
    }

    protected function baseUri(): string
    {
        return 'https://api.bring.com/shippingguide/v2/products';
    }

    /** @return array<string, mixed> */
    protected function queryParameters(): array
    {
        return $this->request->toQuery();
    }

    /** @param array<mixed, mixed> $decoded */
    protected function parseDecoded(array $decoded): ProductsResponse
    {
        return ProductsResponse::fromArray($decoded);
    }
}
