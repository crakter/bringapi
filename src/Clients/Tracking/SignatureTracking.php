<?php

/*
 * This file is part of the BringApi package.
 *
 * (c) Martin Madsen <crakter@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Crakter\BringApi\Clients\Tracking;

use Crakter\BringApi\DefaultData\ClientUrls;
use Crakter\BringApi\Clients\Base;
use Crakter\BringApi\Clients\ClientsInterface;
use Crakter\BringApi\DefaultData\HttpMethods;

/**
 * BringApi GenerateReport
 *
 * A Client to send request to Bring API get signature endpoint
 *
 * Quick setup: <code>$signatureTracking = new SignatureTracking();</code>
 *
 * @author Martin Madsen <crakter@gmail.com>
 */
class SignatureTracking extends Base implements ClientsInterface
{
    /**
     * @var string $clientUrl    The clients url
     */
    protected $clientUrl = ClientUrls::TRACKING_SIGNATURE;

    /**
     * @var string $httpMethod  The Method for HTTP
     */
    protected $httpMethod = HttpMethods::GET;

    /**
     * @var string $signatureLink   The link to the signature
     */
    protected $signatureLink;

    /**
     * Gets the SignatureLink for clientUrl
     * @example api/signatur.png?kollinummer=3707xxxxxxxxx&dateTimeIso=2017-05-30T10:03:56+02:00
     * @return string
     */
    public function getSignatureLink(): string
    {
        return $this->signatureLink;
    }

    /**
     * Sets the signatureLink for clientUrl
     * @param string $signatureLink
     * @example api/signatur.png?kollinummer=3707xxxxxxxxx&dateTimeIso=2017-05-30T10:03:56+02:00
     * @return ClientsInterface All clients must implement ClientsInterface
     */
    public function setSignatureLink(string $signatureLink): ClientsInterface
    {
        $this->signatureLink = $signatureLink;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function processClientUrlVariables(): ClientsInterface
    {
        $this->setClientUrlVariables($this->getSignatureLink());

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
