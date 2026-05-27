<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Booking;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Http\HeaderNames;
use Bring\Api\Http\HttpMethod;

/**
 * POST https://api.bring.com/booking/api/pickupOrder
 *
 * @extends AbstractJsonEndpoint<PickupResponse>
 */
final class PickupEndpoint extends AbstractJsonEndpoint
{
    public function __construct(private readonly PickupRequest $request)
    {
    }

    public function method(): HttpMethod
    {
        return HttpMethod::POST;
    }

    protected function baseUri(): string
    {
        return 'https://api.bring.com/booking/api/pickupOrder';
    }

    /** @return array<mixed, mixed>|null */
    protected function jsonBody(): ?array
    {
        return $this->request->toArray();
    }

    /** @return array<string, string> */
    protected function extraHeaders(): array
    {
        return $this->request->testIndicator ? [HeaderNames::TEST_MODE => 'true'] : [];
    }

    /** @param array<mixed, mixed> $decoded */
    protected function parseDecoded(array $decoded): PickupResponse
    {
        return PickupResponse::fromArray($decoded);
    }
}
