<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Address;

use Bring\Api\Enum\Country;
use Bring\Api\Http\Transport;

/**
 * Address API — https://developer.bring.com/api/postal-code/.
 *
 * Modern replacement for the legacy /shippingguide/api/postalCode endpoint.
 * Supports per-country postal-code lookups, suggestions, and mailbox-delivery
 * date queries (NO/SE/DK/FI/IS/NL/DE/BE/US/FO/GL/SJ — endpoint validates).
 */
final class AddressApi
{
    public function __construct(private readonly Transport $transport)
    {
    }

    public function postalCode(Country $country, string $postalCode): PostalCodeLookupResponse
    {
        return $this->transport->send(new PostalCodeLookupEndpoint($country, $postalCode));
    }

    public function suggestions(Country $country, string $prefix): SuggestionsResponse
    {
        return $this->transport->send(new SuggestionsEndpoint($country, $prefix));
    }

    public function mailboxDeliveryDates(Country $country, string $postalCode): MailboxDeliveryDatesResponse
    {
        return $this->transport->send(new MailboxDeliveryDatesEndpoint($country, $postalCode));
    }
}
