<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Tracking;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Http\HttpMethod;

/**
 * GET https://tracking.bring.com/api/v2/tracking.json?q={query}
 *
 * @extends AbstractJsonEndpoint<TrackingResponse>
 */
final class TrackEndpoint extends AbstractJsonEndpoint
{
    public function __construct(private readonly string $query)
    {
    }

    public function method(): HttpMethod
    {
        return HttpMethod::GET;
    }

    protected function baseUri(): string
    {
        return 'https://tracking.bring.com/api/v2/tracking.json';
    }

    /** @return array<string, mixed> */
    protected function queryParameters(): array
    {
        return ['q' => $this->query];
    }

    /** @param array<mixed, mixed> $decoded */
    protected function parseDecoded(array $decoded): TrackingResponse
    {
        return TrackingResponse::fromArray($decoded);
    }
}
