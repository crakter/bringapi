<?php

declare(strict_types=1);

/*
 * This file is part of the BringApi package.
 *
 * (c) Martin Madsen <crakter@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Crakter\BringApi\Clients\Booking;

use Crakter\BringApi\DefaultData\ClientUrls;
use Crakter\BringApi\Clients\Base;
use Crakter\BringApi\Clients\ClientsInterface;
use Crakter\BringApi\DefaultData\HttpMethods;
use Crakter\BringApi\Exception\BringClientException;
use Crakter\BringApi\Exception\ApiEntityNotCorrectException;

/**
 * BringApi OrderPickup
 *
 * A Client to send request to Bring API book shipment endpoint
 *
 * Quick setup: <code>$orderPickup = new OrderPickup();</code>
 *
 * @author Martin Madsen <crakter@gmail.com>
 */
class OrderPickup extends Base implements ClientsInterface
{
    /**
     * @var string $clientUrl    The clients url
     */
    protected string $clientUrl = ClientUrls::BOOKING_ORDER_PICKUPS;

    /**
     * @var string $httpMethod  The Method for HTTP
     */
    protected string $httpMethod = HttpMethods::POST;

    /**
     * {@inheritdoc}
     */
    public function processClientUrlVariables(): ClientsInterface
    {
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
     * @throws ApiEntityNotCorrectException
     */
    public function processEntity(): ClientsInterface
    {
        try {
            $this->getApiEntity();
        } catch (\Throwable) {
            throw new ApiEntityNotCorrectException('Api Entity needs to be set.');
        }
        $this->setOptionsJson($this->getApiEntity()->toArray());

        return $this;
    }
}
