<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\PostalCode;

use Bring\Api\Enum\Country;
use Bring\Api\Http\Transport;

/**
 * Legacy Bring postal-code lookup. Kept for compatibility — for new code,
 * use {@see \Bring\Api\Endpoint\Address\AddressApi}.
 *
 * @deprecated Bring's preferred replacement is the Address API.
 */
final class PostalCodeApi
{
    public function __construct(private readonly Transport $transport)
    {
    }

    public function lookup(string $postalCode, ?Country $country = null): PostalCodeResponse
    {
        return $this->transport->send(new PostalCodeEndpoint($postalCode, $country));
    }
}
