<?php

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

/**
 * BringApi ListCustomers
 *
 * A Client to send request to Bring API book shipment endpoint
 *
 * Quick setup: <code>$listCustomers = new ListCustomers();</code>
 *
 * @author Martin Madsen <crakter@gmail.com>
 */
class ListCustomers extends Base implements ClientsInterface
{
    /**
     * @var string $clientUrl    The clients url
     */
    protected $clientUrl = ClientUrls::BOOKING_LIST_CUSTOMER_NUMBERS;

    /**
     * @var string $httpMethod  The Method for HTTP
     */
    protected $httpMethod = HttpMethods::GET;

    /**
     * Get the list of products available to customer
     * @param  string $customerNumber number of customer you want
     * @return array  array of products or empty array if wrong
     */
    public function getProductsCustomer(string $customerNumber): array
    {
        foreach ($this->toArray()['customers'] as $key => $val) {
            if ($val['customerNumber'] == $customerNumber) {
                return $val['products'];
            }
        }

        return [];
    }

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
        return $this;
    }
}
