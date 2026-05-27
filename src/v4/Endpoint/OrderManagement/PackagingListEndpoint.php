<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\OrderManagement;

use Bring\Api\Endpoint\AbstractJsonEndpoint;
use Bring\Api\Http\HttpMethod;

/**
 * POST https://api.bring.com/po/api/v1/packaginglist
 *
 * @extends AbstractJsonEndpoint<PackagingListResponse>
 */
final class PackagingListEndpoint extends AbstractJsonEndpoint
{
    /** @param array<string, mixed> $payload */
    public function __construct(private readonly array $payload)
    {
    }

    #[\Override]
    public function method(): HttpMethod
    {
        return HttpMethod::POST;
    }

    #[\Override]
    protected function baseUri(): string
    {
        return 'https://api.bring.com/po/api/v1/packaginglist';
    }

    /** @return array<mixed, mixed>|null */
    #[\Override]
    protected function jsonBody(): ?array
    {
        return $this->payload;
    }

    /** @param array<mixed, mixed> $decoded */
    #[\Override]
    protected function parseDecoded(array $decoded): PackagingListResponse
    {
        return PackagingListResponse::fromArray($decoded);
    }
}
