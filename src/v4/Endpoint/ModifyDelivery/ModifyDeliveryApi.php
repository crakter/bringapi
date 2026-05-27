<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\ModifyDelivery;

use Bring\Api\Http\Transport;

/**
 * Modify Delivery API — https://developer.bring.com/api/modify-delivery/.
 *
 * Bring serves Norway, Sweden and Denmark for this API. Operations launched
 * incrementally (contact-detail updates added January 2025). Each method
 * targets one PUT/POST against /modifydelivery/api/...
 */
final class ModifyDeliveryApi
{
    public function __construct(private readonly Transport $transport)
    {
    }

    public function stopShipment(StopShipmentRequest $request): ModifyDeliveryResponse
    {
        return $this->transport->send(new StopShipmentEndpoint($request));
    }

    public function changeAddress(ChangeAddressRequest $request): ModifyDeliveryResponse
    {
        return $this->transport->send(new ChangeAddressEndpoint($request));
    }

    public function updateContactDetails(UpdateContactRequest $request): ModifyDeliveryResponse
    {
        return $this->transport->send(new UpdateContactEndpoint($request));
    }
}
