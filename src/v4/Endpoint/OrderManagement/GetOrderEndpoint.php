<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\OrderManagement;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Http\HttpMethod;

/**
 * GET https://api.bring.com/po/api/v1/omorder/{customerNumber}/{orderNumber}
 *
 * @extends AbstractJsonEndpoint<OrderResponse>
 */
final class GetOrderEndpoint extends AbstractJsonEndpoint
{
    public function __construct(
        private readonly string $customerNumber,
        private readonly string $orderNumber,
    ) {
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
            'https://api.bring.com/po/api/v1/omorder/%s/%s',
            rawurlencode($this->customerNumber),
            rawurlencode($this->orderNumber),
        );
    }

    /** @param array<mixed, mixed> $decoded */
    #[\Override]
    protected function parseDecoded(array $decoded): OrderResponse
    {
        return OrderResponse::fromArray($decoded);
    }
}
