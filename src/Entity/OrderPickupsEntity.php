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
namespace Crakter\BringApi\Entity;

/**
 * BringApi OrderPickupsEntity
 *
 * An class to supply correct information to Bring Api servers
 *
 * Quick setup: <code>$orderPickups = (new OrderPickupsEntity)
 *                     ->setTestIndicator(false);</code>
 *
 * @property string $cargoInformation                   Information about the cargo
 * @property string $parcelsInternationalInformation    Information about the cargo
 * @method ApiEntityInterface setTestIndicator(bool $bool)
 * @method bool getTestIndicator()
 * @method ApiEntityInterface setCustomerInformation(array $array)
 * @method array getCustomerInformation()
 * @method ApiEntityInterface setPickupAddress(array $array)
 * @method array getPickupAddress()
 * @method string getPickupDate()
 * @method string getPickupIsReadyAtTime()
 * @method ApiEntityInterface setCountry(string $string)
 * @method string getCountryCode()
 * @method ApiEntityInterface setParcelsInformation(array $array)
 * @method array getParcelsInformation()
 * @author Martin Madsen <crakter@gmail.com>
 */
class OrderPickupsEntity extends ApiEntityBase implements ApiEntityInterface
{
    /**
     * @var bool $testIndicator This needs to be set to true if testing or false if production
     */
    public $testIndicator = true;

    /**
     * @var array $customerInformation This needs to be set to a array of information about company. <code>['customerNumber' => 'PARCELS_NORWAY-12340056789', 'companyName' => 'Norsk Bedrift AS']</code>
     */
    public $customerInformation = [];

    /**
     * @var array $pickupAddress This needs to be set to an array of information about pickup address.
     */
    public $pickupAddress = [];
    /**
     * @var string $pickupDate This needs to be set to date of pickup. YYYY-MM-DD
     */
    public $pickupDate;

    /**
     * @var string $pickupIsReadyAtTime can be set to time of pickup HH:mm:ss
     */
    public $pickupIsReadyAtTime;

    /**
     * @var string $countryCode This needs to be set to the current country. <code>Countries::NORWAY</code>
     */
    public $countryCode;

    /**
     * @var array $parcelsInformation This needs to be set to array with information about the packages to be picked up. <code>['numberOfPackages' => 1, 'numberOfPostContainers' => 0, 'numberOfPallets' => 1]</code>
     */
    public $parcelsInformation = [];

    /**
     * Sets correct input format
     */
    public function setPickupDate(\DateTime $dateTime): ApiEntityInterface
    {
        $this->pickupDate = $dateTime->format('Y-m-d');

        return $this;
    }

    /**
     * Sets correct input format
     */
    public function setPickupIsReadyAtTime(\DateTime $dateTime): ApiEntityInterface
    {
        $this->pickupIsReadyAtTime = $dateTime->format('H:m:s');

        return $this;
    }
}
