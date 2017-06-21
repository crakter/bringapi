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
use Crakter\BringApi\Exception\ValueNotSetException;
use Crakter\BringApi\Exception\BringClientException;

/**
 * BringApi ListAvailableReportsCustomer
 *
 * A Client to send request to Bring API reports available reports for client Id
 *
 * Quick setup: <code>$listAvailableReportsCustomer = new ListAvailableReportsCustomer();</code>
 *
 * @author Martin Madsen <crakter@gmail.com>
 */
class ListAvailableReportsCustomer extends Base implements ClientsInterface
{
    /**
     * CustomerId to be used in url
     * @var string $customerId
     */
    protected $customerId;

    /**
     * @var string $clientUrl    The clients url
     */
    protected $clientUrl = ClientUrls::REPORTS_LIST_AVAILABLE_REPORTS_CUSTOMER;

    /**
     * @var string $httpMethod  The Method for HTTP
     */
    protected $httpMethod = HttpMethods::GET;

    /**
     * Gets the parameters required for the report.
     * @param string $reportId
     * @example Array (0 => ['name' => "fromDate", 'type' => "date",
     *                      'description' => "Startdate of the report. Format: DD.MM.YYYY"]
     *                )
     * @see ListAvailableReportsCustomer::getReports()
     * @throws ValueNotSetException if reportId is not returned from Bring Api
     * @return array
     */
    public function getParameters(string $reportId): array
    {
        $array = $this->getReports();
        foreach ($array as $report) {
            if ($reportId == $report['id']) {
                $parameters = (array) $report['parameter'];
            }
        }
        if (!isset($parameters)) {
            throw new ValueNotSetException(sprintf('%s is not returned from Bring API as available report', $reportId));
        }

        return $parameters;
    }

    /**
     * Gets the parameters required for the report.
     * @example Array (0 => ['name' => "fromDate", 'type' => "date",
     *                      'description' => "Startdate of the report. Format: DD.MM.YYYY"]
     *                )
     * @see Base::toArray()
     * @return array
     */
    public function getReports(): array
    {
        return (array) $this->toArray()['reports'];
    }

    /**
     * Sets the customerId for clientUrl
     * @param string $customerId
     * @example PARCELS_NORWAY-00012341234
     * @return ClientsInterface All clients must implement ClientsInterface
     */
    public function setCustomerId(string $customerId): ClientsInterface
    {
        $this->customerId = $customerId;

        return $this;
    }

    /**
     * Gets the customerId for clientUrl
     * @example PARCELS_NORWAY-00012341234
     * @return string
     */
    public function getCustomerId(): string
    {
        return $this->customerId;
    }

    /**
     * {@inheritdoc}
     */
    public function processClientUrlVariables(): ClientsInterface
    {
        $this->setClientUrlVariables($this->getCustomerId(), $this->getEndPoint());

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
