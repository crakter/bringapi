<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\OrderManagement;

use Bring\Api\Http\Transport;

/**
 * Order Management API (REST endpoints) — https://developer.bring.com/api/order-management/.
 *
 * The SOAP variant (api.bring.com/po/ws) is intentionally out of scope; use
 * one of the dedicated PHP SOAP wrappers if you need it.
 */
final class OrderManagementApi
{
    public function __construct(private readonly Transport $transport)
    {
    }

    public function getOrder(string $customerNumber, string $orderNumber): OrderResponse
    {
        return $this->transport->send(new GetOrderEndpoint($customerNumber, $orderNumber));
    }

    /** @param array<string, mixed> $payload Bring packaging-list schema */
    public function sendPackagingList(array $payload): PackagingListResponse
    {
        return $this->transport->send(new PackagingListEndpoint($payload));
    }
}
