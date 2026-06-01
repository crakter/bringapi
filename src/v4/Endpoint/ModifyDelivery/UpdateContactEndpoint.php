<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\ModifyDelivery;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Http\HttpMethod;

/**
 * POST https://www.mybring.com/modifydelivery/api/update-contact-details
 *
 * @extends AbstractJsonEndpoint<ModifyDeliveryResponse>
 */
final class UpdateContactEndpoint extends AbstractJsonEndpoint
{
    public function __construct(private readonly UpdateContactRequest $request)
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
        return 'https://www.mybring.com/modifydelivery/api/update-contact-details';
    }

    /** @return array<mixed, mixed>|null */
    #[\Override]
    protected function jsonBody(): ?array
    {
        return $this->request->toArray();
    }

    /** @param array<mixed, mixed> $decoded */
    #[\Override]
    protected function parseDecoded(array $decoded): ModifyDeliveryResponse
    {
        return ModifyDeliveryResponse::fromArray($decoded);
    }
}
