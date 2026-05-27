<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\PostalCode;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Enum\Country;
use Bring\Api\Http\HttpMethod;

/**
 * GET https://api.bring.com/shippingguide/api/postalCode.json
 *
 * @extends AbstractJsonEndpoint<PostalCodeResponse>
 */
final class PostalCodeEndpoint extends AbstractJsonEndpoint
{
    public function __construct(
        private readonly string $postalCode,
        private readonly ?Country $country = null,
    ) {
    }

    public function method(): HttpMethod
    {
        return HttpMethod::GET;
    }

    protected function baseUri(): string
    {
        return 'https://api.bring.com/shippingguide/api/postalCode.json';
    }

    /** @return array<string, mixed> */
    protected function queryParameters(): array
    {
        $q = ['pnr' => $this->postalCode];
        if ($this->country !== null) {
            $q['country'] = $this->country->value;
        }

        return $q;
    }

    /** @param array<mixed, mixed> $decoded */
    protected function parseDecoded(array $decoded): PostalCodeResponse
    {
        return PostalCodeResponse::fromArray($decoded);
    }
}
