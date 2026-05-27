<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Booking;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Http\HeaderNames;
use Bring\Api\Http\HttpMethod;

/**
 * POST https://api.bring.com/booking/api/booking
 *
 * @extends AbstractJsonEndpoint<BookingResponse>
 */
final class BookEndpoint extends AbstractJsonEndpoint
{
    public function __construct(private readonly BookingRequest $request)
    {
    }

    public function method(): HttpMethod
    {
        return HttpMethod::POST;
    }

    protected function baseUri(): string
    {
        return 'https://api.bring.com/booking/api/booking';
    }

    /** @return array<mixed, mixed>|null */
    protected function jsonBody(): ?array
    {
        return $this->request->toArray();
    }

    /** @return array<string, string> */
    protected function extraHeaders(): array
    {
        return $this->request->testIndicator ? [HeaderNames::TEST_MODE => 'true'] : [];
    }

    /** @param array<mixed, mixed> $decoded */
    protected function parseDecoded(array $decoded): BookingResponse
    {
        return BookingResponse::fromArray($decoded);
    }
}
