<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Shipping;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Http\HttpMethod;

/**
 * POST https://api.bring.com/shippingguide/api/v2/products
 *
 * Returns full product details — price + delivery time + GUI info when the
 * matching flags are set on the request.
 *
 * Bring documents only the POST form of the Shipping Guide. Its body lists
 * every package (see {@see PriceRequest::toJsonBody()}); the old GET form
 * priced a multi-package request as one package.
 *
 * @extends AbstractJsonEndpoint<ProductsResponse>
 */
final class ProductsEndpoint extends AbstractJsonEndpoint
{
    public function __construct(private readonly PriceRequest $request)
    {
    }

    #[\Override]
    public function method(): HttpMethod
    {
        return HttpMethod::POST;
    }

    #[\Override]
    protected function baseUri(): string
    {
        return 'https://api.bring.com/shippingguide/api/v2/products';
    }

    /** @return array<string, mixed> */
    #[\Override]
    protected function jsonBody(): array
    {
        return $this->request->toJsonBody();
    }

    /** @param array<mixed, mixed> $decoded */
    #[\Override]
    protected function parseDecoded(array $decoded): ProductsResponse
    {
        return ProductsResponse::fromArray($decoded);
    }
}
