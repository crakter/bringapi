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
namespace Crakter\BringApi\Clients\Tracking;

use Crakter\BringApi\DefaultData\ClientUrls;
use Crakter\BringApi\Clients\Base;
use Crakter\BringApi\Clients\ClientsInterface;
use Crakter\BringApi\DefaultData\HttpMethods;
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
class TrackingEndpoint extends Base implements ClientsInterface
{
    /**
     * @var string $clientUrl    The clients url
     */
    protected string $clientUrl = ClientUrls::TRACKING_NOT_LOGGED_IN;

    /**
     * @var string $alternativeAuthorizedUrl    The alternative clients url if logged in
     */
    protected string $alternativeAuthorizedUrl = ClientUrls::TRACKING_LOGGED_IN;

    /**
     * @var string $httpMethod  The Method for HTTP
     */
    protected string $httpMethod = HttpMethods::GET;

    /**
     * Gets the last position of the goods, only for tracking number - not for reference as it can have multiple outputs.
     * @example
     * @see Base::toArray()
     */
    public function getLastPosition(): array
    {
        return $this->toArray()['consignmentSet'][0]['packageSet'][0]['eventSet'][0];
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
        $this->toArray();

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
