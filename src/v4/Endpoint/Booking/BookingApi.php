<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Booking;

use Bring\Api\Http\Transport;

/**
 * Booking API — https://developer.bring.com/api/booking/.
 */
final class BookingApi
{
    public function __construct(private readonly Transport $transport)
    {
    }

    /**
     * Book one or more consignments. Pass {@see BookingRequest::asTest()} (or
     * {@see \Bring\Api\ApiClient::withTestMode()} on the facade) to set
     * X-Bring-Test-Indicator so Bring does not actually generate labels.
     */
    public function book(BookingRequest $request): BookingResponse
    {
        return $this->transport->send(new BookEndpoint($request));
    }

    public function pickup(PickupRequest $request): PickupResponse
    {
        return $this->transport->send(new PickupEndpoint($request));
    }

    public function customers(): CustomersResponse
    {
        return $this->transport->send(new CustomersEndpoint());
    }
}
