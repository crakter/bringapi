<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Address;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Enum\Country;
use Bring\Api\Http\HttpMethod;

/**
 * GET https://api.bring.com/address/api/{country}/postal-codes/suggestions?postal_code={prefix}
 *
 * @extends AbstractJsonEndpoint<SuggestionsResponse>
 */
final class SuggestionsEndpoint extends AbstractJsonEndpoint
{
    public function __construct(
        private readonly Country $country,
        private readonly string $prefix,
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
            'https://api.bring.com/address/api/%s/postal-codes/suggestions',
            strtolower($this->country->value),
        );
    }

    /** @return array<string, mixed> */
    #[\Override]
    protected function queryParameters(): array
    {
        return ['postal_code' => $this->prefix];
    }

    /** @param array<mixed, mixed> $decoded */
    #[\Override]
    protected function parseDecoded(array $decoded): SuggestionsResponse
    {
        return SuggestionsResponse::fromArray($decoded);
    }
}
