<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Shipping;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Http\HttpMethod;

/**
 * POST https://api.bring.com/shippingguide/api/v2/products
 *
 * Prices: the request is sent with `withPrice` on, whatever it says.
 *
 * Bring documents only the POST form of the Shipping Guide. Its body lists
 * every package (see {@see PriceRequest::toJsonBody()}); the old GET form
 * priced a multi-package request as one package.
 *
 * @extends AbstractJsonEndpoint<PriceResponse>
 */
final class PriceEndpoint extends AbstractJsonEndpoint
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
        return $this->request->toJsonBody(withPrice: true);
    }

    /** @param array<mixed, mixed> $decoded */
    #[\Override]
    protected function parseDecoded(array $decoded): PriceResponse
    {
        return PriceResponse::fromArray($decoded);
    }
}
