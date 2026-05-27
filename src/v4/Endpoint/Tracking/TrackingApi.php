<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Tracking;

use Bring\Api\Http\Transport;

/**
 * Tracking API — https://developer.bring.com/api/tracking/.
 *
 * Since May 2024 Bring requires Mybring authentication on all tracking calls;
 * this library always sends the credentials when they are configured.
 */
final class TrackingApi
{
    public function __construct(private readonly Transport $transport)
    {
    }

    public function track(string $query): TrackingResponse
    {
        return $this->transport->send(new TrackEndpoint($query));
    }

    /**
     * Returns the raw signature image bytes (PNG by default).
     */
    public function signature(string $trackingId): string
    {
        return $this->transport->send(new SignatureEndpoint($trackingId));
    }
}
