<?php

/*
 * This file is part of the BringApi package.
 *
 * (c) Martin Madsen <crakter@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Crakter\BringApi\Clients\ShippingGuide;

use Crakter\BringApi\DefaultData\ClientUrls;
use Crakter\BringApi\Clients\Base;
use Crakter\BringApi\Clients\ClientsInterface;
use Crakter\BringApi\DefaultData\HttpMethods;
use Crakter\BringApi\Exception\BringClientException;
use Crakter\BringApi\Exception\ApiEntityNotCorrectException;

/**
 * BringApi ShipmentPrices
 *
 * A Client to send request to Bring API generate report endpoint
 *
 * Quick setup: <code>$shipmentPrices = new ShipmentPrices();</code>
 *
 * @author Martin Madsen <crakter@gmail.com>
 */
class ShipmentPrices extends Base implements ClientsInterface
{
    /**
     * @var string $clientUrl    The clients url
     */
    protected $clientUrl = ClientUrls::SHIPPINGGUIDE_SHIPMENT_PRICES;

    /**
     * @var string $httpMethod  The Method for HTTP
     */
    protected $httpMethod = HttpMethods::GET;

    /**
     * {@inheritdoc}
     */
    public function processClientUrlVariables(): ClientsInterface
    {
        $this->setClientUrlVariables($this->getEndPoint());

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function checkErrors(): ClientsInterface
    {
        $checkIfNotError = $this->isJson($this->toJson());

        if ($checkIfNotError === false) {
            throw new BringClientException($this->toJson());
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function processEntity(): ClientsInterface
    {
        try {
            $this->getApiEntity();
        } catch (\Throwable $e) {
            throw new ApiEntityNotCorrectException('Api Entity needs to be set.');
        }
        $this->setOptionsQuery($this->apiEntity->toArray());

        return $this;
    }
}
