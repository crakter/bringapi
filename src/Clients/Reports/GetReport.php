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

/**
 * BringApi GetReport
 *
 * A Client to send request to Bring API to get the report back, XML is preferred here.
 *
 * Quick setup: <code>$getReport = new GetReport();</code>
 *
 * @author Martin Madsen <crakter@gmail.com>
 */
class GetReport extends Base implements ClientsInterface
{
    /**
     * @var string $clientUrl    The clients url
     */
    protected $clientUrl = ClientUrls::REPORTS_GET_REPORT;

    /**
     * @var string $httpMethod  The Method for HTTP
     */
    protected $httpMethod = HttpMethods::GET;

    /**
     * ReportId to be used in url
     * @var string $reportId
     */
    protected $reportId;

    public function __construct(ApiEntityInterface $apiEntity = null, AuthorizationInterface $authorizationModule = null, ClientInterface $client = null)
    {
        parent::__construct($apiEntity, $authorizationModule, $client);
        //Sets the reponse to XML as default - can be changed to XLS by ->setReturnXls()
        $this->setReturnXml();
    }

    /**
     * Sets the reportId for clientUrl
     * @param string $reportId
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
     * @return string
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
        $array = $this->toArray();

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
