<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Booking;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Exception\InvalidArgumentException;
use Bring\Api\Http\HttpMethod;

/**
 * GET https://api.bring.com/booking/api/customers.{format}
 *
 * @extends AbstractJsonEndpoint<CustomersResponse>
 */
final class CustomersEndpoint extends AbstractJsonEndpoint
{
    public function __construct(private readonly string $format = 'json')
    {
        if (!in_array($format, ['json', 'xml'], true)) {
            throw new InvalidArgumentException('CustomersEndpoint: format must be json or xml.');
        }
    }

    public function method(): HttpMethod
    {
        return HttpMethod::GET;
    }

    protected function baseUri(): string
    {
        return sprintf('https://api.bring.com/booking/api/customers.%s', $this->format);
    }

    /** @param array<mixed, mixed> $decoded */
    protected function parseDecoded(array $decoded): CustomersResponse
    {
        return CustomersResponse::fromArray($decoded);
    }
}
