<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\ModifyDelivery;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Http\HttpMethod;

/**
 * POST https://www.mybring.com/modifydelivery/api/stop-shipment
 *
 * @extends AbstractJsonEndpoint<ModifyDeliveryResponse>
 */
final class StopShipmentEndpoint extends AbstractJsonEndpoint
{
    public function __construct(private readonly StopShipmentRequest $request)
    {
    }

    public function method(): HttpMethod
    {
        return HttpMethod::POST;
    }

    protected function baseUri(): string
    {
        return 'https://www.mybring.com/modifydelivery/api/stop-shipment';
    }

    /** @return array<mixed, mixed>|null */
    protected function jsonBody(): ?array
    {
        return $this->request->toArray();
    }

    /** @param array<mixed, mixed> $decoded */
    protected function parseDecoded(array $decoded): ModifyDeliveryResponse
    {
        return ModifyDeliveryResponse::fromArray($decoded);
    }
}
