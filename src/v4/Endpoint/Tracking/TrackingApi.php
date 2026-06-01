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
     * Returns the raw signature image bytes (PNG).
     *
     * Pass the path Bring returns on a {@see TrackedEvent::$signatureLink},
     * not a tracking id — Bring serves signatures from a separate route
     * that already encodes the package id and capture timestamp in its
     * query string.
     */
    public function signature(string $signatureLinkPath): string
    {
        return $this->transport->send(new SignatureEndpoint($signatureLinkPath));
    }

    /**
     * Returns the full signature URL without fetching the bytes — useful
     * when you want to embed the signature into an already-authenticated
     * UI via `<img src="…">` and let the browser do the GET.
     *
     * Pass the same {@see TrackedEvent::$signatureLink} value you would
     * give to {@see signature()}.
     */
    public function signatureUrl(string $signatureLinkPath): string
    {
        return (new SignatureEndpoint($signatureLinkPath))->url();
    }
}
