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
    protected $clientUrl = ClientUrls::REPORTS_LIST_INVOICE_NUMBERS;

    /**
     * @var string $httpMethod  The Method for HTTP
     */
    protected $httpMethod = HttpMethods::GET;

    /**
     * @var string $reportId    customerOrGroupId to be used in url
     */
    protected $customerOrGroupId;

    /**
     * Gets the available invoices.
     * @example Array([['label' => '702941479 (10/18/2015)', 'id' => '702941479', 'year' => 2015, 'month' => 10, 'day' => 18]])
     * @see Base::toArray()
     * @return array
     */
    public function getInvoiceNumbers(): string
    {
        return $this->toArray()['invoiceNumbers'];
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
     * Sets the customerOrGroupId for clientUrl
     * @param string $customerOrGroupId
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
     * @return string
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
        $this->setClientUrlVariables($this->getCustomerId(), $this->getCustomerOrGroupId(), $this->getEndPoint());

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
