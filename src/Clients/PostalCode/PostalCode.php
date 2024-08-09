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
namespace Crakter\BringApi\Clients\PostalCode;

use Crakter\BringApi\DefaultData\ClientUrls;
use Crakter\BringApi\Clients\Base;
use Crakter\BringApi\Clients\ClientsInterface;
use Crakter\BringApi\DefaultData\HttpMethods;
use Crakter\BringApi\Exception\BringClientException;
use Crakter\BringApi\Exception\ApiEntityNotCorrectException;

/**
 * BringApi GenerateReport
 *
 * A Client to send request to Bring API generate report endpoint
 *
 * Quick setup: <code>$generateReport = new GenerateReport();</code>
 *
 * @author Martin Madsen <crakter@gmail.com>
 */
class PostalCode extends Base implements ClientsInterface
{
    /**
     * @var string $clientUrl    The clients url
     */
    protected string $clientUrl = ClientUrls::POSTALCODE_API;

    /**
     * @var string $httpMethod  The Method for HTTP
     */
    protected string $httpMethod = HttpMethods::GET;

    /**
     * Check if the return from server is NORMAL postal code
     * @return bool true/false
     */
    public function checkIfNormal(): bool
    {
        return $this->toArray()['postalCodeType'] == 'NORMAL';
    }

    /**
     * Check if the return from server is UNKNOWN postal code
     * @return bool true/false
     */
    public function checkIfUnknown(): bool
    {
        return $this->toArray()['postalCodeType'] == 'UNKNOWN';
    }

    /**
     * Check if the return from server is POBOX postal code
     * @return bool true/false
     */
    public function checkIfPoBox(): bool
    {
        return $this->toArray()['postalCodeType'] == 'POBOX';
    }

    /**
     * Check if the return from server is SPECIALCUSTOMER postal code
     * @return bool true/false
     */
    public function checkIfSpecialCustomer(): bool
    {
        return $this->toArray()['postalCodeType'] == 'SPECIALCUSTOMER';
    }

    /**
     * Check if the return from server is SPECIALNOSTREET postal code
     * @return bool true/false
     */
    public function checkIfSpecialNoStreet(): bool
    {
        return $this->toArray()['postalCodeType'] == 'SPECIALNOSTREET';
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
        try {
            $this->getApiEntity();
        } catch (\Throwable) {
            throw new ApiEntityNotCorrectException('Api Entity needs to be set.');
        }
        $this->setOptionsQuery($this->apiEntity->toArray());

        return $this;
    }
}
