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
 * BringApi GetReport
 *
 * A Client to send request to Bring API to get list of invoice numbers
 *
 * Quick setup: <code>$listInvoiceNumbers = new ListInvoiceNumbers();</code>
 *
 * @author Martin Madsen <crakter@gmail.com>
 */
class ListInvoiceNumbers extends Base implements ClientsInterface
{
    /**
     * @var string $clientUrl    The clients url
     */
    protected string $clientUrl = ClientUrls::REPORTS_LIST_INVOICE_NUMBERS;

    /**
     * @var string $httpMethod  The Method for HTTP
     */
    protected string $httpMethod = HttpMethods::GET;

    /**
     * @var string $reportId    customerOrGroupId to be used in url
     */
    protected string $customerOrGroupId;

    /**
     * Gets the available invoices.
     * @example Array([['label' => '702941479 (10/18/2015)', 'id' => '702941479', 'year' => 2015, 'month' => 10, 'day' => 18]])
     * @see Base::toArray()
     */
    public function getInvoiceNumbers(): array
    {
        return $this->toArray()['invoices'];
    }

    /**
     * Sets the customerOrGroupId for clientUrl
     * @example PARCELS_NORWAY-00012341234
     * @return ClientsInterface All clients must implement ClientsInterface
     */
    public function setCustomerOrGroupId(string $customerOrGroupId): ClientsInterface
    {
        $this->customerOrGroupId = $customerOrGroupId;

        return $this;
    }

    /**
     * Gets the reportId for clientUrl
     * @example PARCELS_NORWAY-00012341234
     */
    public function getCustomerOrGroupId(): string
    {
        return $this->customerOrGroupId;
    }

    /**
     * {@inheritdoc}
     */
    public function processClientUrlVariables(): ClientsInterface
    {
        $this->setClientUrlVariables($this->getCustomerOrGroupId(), $this->getEndPoint());

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
        if ($this->getApiEntity()) {
            $this->setOptionsQuery($this->apiEntity->toArray());
        }

        return $this;
    }
}
