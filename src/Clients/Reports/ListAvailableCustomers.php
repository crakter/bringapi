<?php

/*
 * This file is part of the BringApi package.
 *
 * (c) Martin Madsen <crakter@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Crakter\BringApi\Clients\Reports;

use Crakter\BringApi\DefaultData\ClientUrls;
use Crakter\BringApi\Clients\Base;
use Crakter\BringApi\Clients\ClientsInterface;
use Crakter\BringApi\DefaultData\HttpMethods;
use Crakter\BringApi\Exception\BringClientException;

/**
 * BringApi ListAvailableCustomers
 *
 * A Client to send request to Bring API reports available customers for client Id
 *
 * Quick setup: <code>$listAvailableCustomers = new ListAvailableCustomers();</code>
 *
 * @author Martin Madsen <crakter@gmail.com>
 */
class ListAvailableCustomers extends Base implements ClientsInterface
{
    /**
     * @var string $clientUrl    The clients url
     */
    protected $clientUrl = ClientUrls::REPORTS_LIST_AVAILABLE_CUSTOMERS;

    /**
     * @var string $httpMethod  The Method for HTTP
     */
    protected $httpMethod = HttpMethods::GET;

    /**
     * Gets the available customers from response.
     * @example Array (0 => ['id' => "PARCELS_NORWAY-00012341234", 'name' => "TEST CUSTOMER",
     *                      'reports' => "https://www.mybring.com/reports/api/generate/PARCELS_NORWAY-00012341234/"]
     *                )
     * @see Base::toArray()
     * @return array
     */
    public function getAvailableCustomers(): array
    {
        $customers = $this->toArray()['customers'];

        return (array) $customers;
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
