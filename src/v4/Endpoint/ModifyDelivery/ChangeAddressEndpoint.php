<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\ModifyDelivery;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Http\HttpMethod;

/**
 * POST https://www.mybring.com/modifydelivery/api/change-address
 *
 * @extends AbstractJsonEndpoint<ModifyDeliveryResponse>
 */
final class ChangeAddressEndpoint extends AbstractJsonEndpoint
{
    public function __construct(private readonly ChangeAddressRequest $request)
    {
    }

    public function method(): HttpMethod
    {
        return HttpMethod::POST;
    }

    protected function baseUri(): string
    {
        return 'https://www.mybring.com/modifydelivery/api/change-address';
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
