<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Address;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Enum\Country;
use Bring\Api\Http\HttpMethod;

/**
 * GET https://api.bring.com/address/api/{country}/postal-codes/{postal-code}/mailbox-delivery-dates
 *
 * @extends AbstractJsonEndpoint<MailboxDeliveryDatesResponse>
 */
final class MailboxDeliveryDatesEndpoint extends AbstractJsonEndpoint
{
    public function __construct(
        private readonly Country $country,
        private readonly string $postalCode,
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
            'https://api.bring.com/address/api/%s/postal-codes/%s/mailbox-delivery-dates',
            strtolower($this->country->value),
            rawurlencode($this->postalCode),
        );
    }

    /** @param array<mixed, mixed> $decoded */
    #[\Override]
    protected function parseDecoded(array $decoded): MailboxDeliveryDatesResponse
    {
        return MailboxDeliveryDatesResponse::fromArray($decoded);
    }
}
