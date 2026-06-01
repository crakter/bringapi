<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Shipping;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Http\HttpMethod;

/**
 * GET https://api.bring.com/shippingguide/v2/products/price
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
        return HttpMethod::GET;
    }

    #[\Override]
    protected function baseUri(): string
    {
        return 'https://api.bring.com/shippingguide/v2/products/price';
    }

    /** @return array<string, mixed> */
    #[\Override]
    protected function queryParameters(): array
    {
        return $this->request->toQuery();
    }

    /** @param array<mixed, mixed> $decoded */
    #[\Override]
    protected function parseDecoded(array $decoded): PriceResponse
    {
        return PriceResponse::fromArray($decoded);
    }
}
