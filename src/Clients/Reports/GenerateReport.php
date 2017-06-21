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
 * BringApi GenerateReport
 *
 * A Client to send request to Bring API generate report endpoint
 *
 * Quick setup: <code>$generateReport = new GenerateReport();</code>
 *
 * @author Martin Madsen <crakter@gmail.com>
 */
class GenerateReport extends Base implements ClientsInterface
{
    /**
     * @var string $clientUrl    The clients url
     */
    protected $clientUrl = ClientUrls::REPORTS_GENERATE;

    /**
     * @var string $httpMethod  The Method for HTTP
     */
    protected $httpMethod = HttpMethods::GET;

    /**
     * CustomerId to be used in url
     * @var string $customerId
     */
    protected $customerId;
    /**
     * ReportTypeId to be used in url
     * @var string $reportTypeId
     */
    protected $reportTypeId;

    /**
     * Gets the available customers from response.
     * @example https://www.mybring.com/reports/api/report/db285042-6e8d-4563-94ca-eb1100706a73/status/
     * @see Base::toArray()
     * @return string
     */
    public function getStatusUrl(): string
    {
        return $this->toArray()['statusUrl'];
    }

    /**
     * Gets the report id from url (we need this to be able to change to JSON in request)
     * @example db285042-6e8d-4563-94ca-eb1100706a73
     * @see Base::toArray()
     * @return string
     */
    public function getReportId(): string
    {
        return explode('/', $this->toArray()['statusUrl'])[6];
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
     * Sets the reportTypeId for clientUrl
     * @param string $reportTypeId
     * @example PARCELS-PRE_NOTIFICATION_RECEIVED
     * @return ClientsInterface All clients must implement ClientsInterface
     */
    public function setReportTypeId(string $reportTypeId): ClientsInterface
    {
        $this->reportTypeId = $reportTypeId;

        return $this;
    }

    /**
     * Gets the customerId for clientUrl
     * @example PARCELS-PRE_NOTIFICATION_RECEIVED
     * @return string
     */
    public function getReportTypeId(): string
    {
        return $this->reportTypeId;
    }

    /**
     * {@inheritdoc}
     */
    public function processClientUrlVariables(): ClientsInterface
    {
        $this->setClientUrlVariables($this->getCustomerId(), $this->getReportTypeId(), $this->getEndPoint());

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
        $this->setOptionsQuery($this->apiEntity->toArray());

        return $this;
    }
}
