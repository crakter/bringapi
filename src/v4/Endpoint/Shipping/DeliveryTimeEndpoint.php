<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Shipping;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Http\HttpMethod;

/**
 * POST https://api.bring.com/shippingguide/api/v2/products
 *
 * Lead times: the request is sent with `withExpectedDelivery` on, whatever it says.
 *
 * Bring documents only the POST form of the Shipping Guide. Its body lists
 * every package (see {@see PriceRequest::toJsonBody()}); the old GET form
 * priced a multi-package request as one package.
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
        return $this->request->toJsonBody(withExpectedDelivery: true);
    }

    /** @param array<mixed, mixed> $decoded */
    #[\Override]
    protected function parseDecoded(array $decoded): DeliveryTimeResponse
    {
        return DeliveryTimeResponse::fromArray($decoded);
    }
}
