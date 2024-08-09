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
namespace Crakter\BringApi\Clients\Reports;

use Crakter\BringApi\DefaultData\ClientUrls;
use Crakter\BringApi\Clients\Base;
use Crakter\BringApi\Clients\ClientsInterface;
use Crakter\BringApi\DefaultData\HttpMethods;
use Crakter\BringApi\Exception\BringClientException;

/**
 * BringApi StatusOfReport
 *
 * A Client to send request to Bring API to check the current status of a report
 *
 * Quick setup: <code>$statusOfReport = new StatusOfReport();</code>
 *
 * @author Martin Madsen <crakter@gmail.com>
 */
class StatusOfReport extends Base implements ClientsInterface
{
    /**
     * @var string $clientUrl    The clients url
     */
    protected string $clientUrl = ClientUrls::REPORTS_CHECK_STATUS_OF_REPORT;

    /**
     * @var string $httpMethod  The Method for HTTP
     */
    protected string $httpMethod = HttpMethods::GET;

    /**
     * ReportId to be used in url
     */
    protected string $reportId;

    /**
     * Gets the available status from response.
     * @example NOT_DONE
     * @see Base::toArray()
     * @return array
     */
    public function getStatus(): string
    {
        return $this->toArray()['status'];
    }

    /**
     * Gets the report id from url (for the fluent interface to work correctly it is needed so you can do ->setReturnXml() and the url is set by client class)
     * @example db285042-6e8d-4563-94ca-eb1100706a73
     * @see Base::toArray()
     */
    public function getResultReportId(): string
    {
        return explode('/', $this->getXmlUrl())[6];
    }

    /**
     * Gets the available XML url from response
     * @example https://www.mybring.com/reports/api/report/db285042-6e8d-4563-94ca-eb1100706a73.xml
     * @see Base::toArray()
     * @see StatusOfReport::checkStatus()
     * @see StatusOfReport::getStatus()
     * @return array
     */
    public function getXmlUrl(): string
    {
        if ($this->checkStatus()) {
            return $this->toArray()['xmlUrl'];
        }
        // Revert back to status if not done.
        return $this->getStatus();
    }

    /**
     * Gets the available xls from response, revert to status if not available
     * @example https://www.mybring.com/reports/api/report/db285042-6e8d-4563-94ca-eb1100706a73.xls
     * @see Base::toArray()
     * @see StatusOfReport::checkStatus()
     * @see StatusOfReport::getStatus()
     * @return array
     */
    public function getXlsUrl(): string
    {
        if ($this->checkStatus()) {
            return $this->toArray()['xlsUrl'];
        }
        // Revert back to status if not done.
        return $this->getStatus();
    }

    /**
     * Check if status is done, if not return false
     * @return bool true if done, false if Failed or Not done
     */
    public function checkStatus(): bool
    {
        return $this->getStatus() === 'DONE';
    }

    /**
     * Sets the reportId for clientUrl
     * @example db285042-6e8d-4563-94ca-eb1100706a73
     * @return ClientsInterface All clients must implement ClientsInterface
     */
    public function setReportId(string $reportId): ClientsInterface
    {
        $this->reportId = $reportId;

        return $this;
    }

    /**
     * Gets the reportId for clientUrl
     * @example db285042-6e8d-4563-94ca-eb1100706a73
     */
    public function getReportId(): string
    {
        return $this->reportId;
    }

    /**
     * {@inheritdoc}
     */
    public function processClientUrlVariables(): ClientsInterface
    {
        $this->setClientUrlVariables($this->getReportId(), $this->getEndPoint());

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
