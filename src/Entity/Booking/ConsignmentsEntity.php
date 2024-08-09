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
namespace Crakter\BringApi\Entity\Booking;

use Crakter\BringApi\Entity\ApiEntityBase;
use Crakter\BringApi\Entity\ApiEntityInterface;

/**
 * BringApi ConsignmentsEntity
 *
 * An class to supply correct information to Bring Api servers
 *
 * Quick setup: <code>$consignment = (new ConsignmentsEntity)
 *                     ->setShippingDateTime('2015-12-04T13:37:00');</code>
 *
 * @property string $purchaseOrder      Optional. Specify when there is any purchase orders associated with the booking. This parameter can be maximum 35 character long.
 * @property string $correlationId      Optional. Specify something to correlate packages which belong to same order
 * @method ApiEntityInterface setShippingDateTime(string $string)
 * @method string getShippingDateTime()
 * @method ApiEntityInterface setParties(array $array)
 * @method string getParties()
 * @method ApiEntityInterface setProduct(array $array)
 * @method array getProduct()
 * @method ApiEntityInterface setPurchaseOrder(string $string)
 * @method string getPurchaseOrder()
 * @method ApiEntityInterface setCorrelationId(string $string)
 * @method string getCorrelationId()
 * @author Martin Madsen <crakter@gmail.com>
 */
class ConsignmentsEntity extends ApiEntityBase implements ApiEntityInterface
{
    /**
     * @var string $shippingDateTime This needs to be set to date for ready to transported. <code>2016-07-26 08:24:09 +0200</code>
     */
    public $shippingDateTime;

    /**
     * @var array $parties This needs to be set to an array of parties. <code>['sender' => [], 'recipient' => []]</code>
     */
    public $parties = [];

    /**
     * @var array $product This needs to be set to an array with information about product. <code>['id' => Products::SERVICEPAKKE, 'customerNumber' => 'PARCELS_NORWAY-10005540322']</code>
     */
    public $product = [];

    /**
     * @var array $packages This needs to be set to an array of packages. <code>[['weightInKg' => 20, 'goodsDescription' => 'Testing equipment']]</code>
     */
    public $packages = [];

    /**
     * Method to add to array of packages.
     * @param  ApiEntityInterface $package Information about the new package
     */
    public function addPackage(array $package): ApiEntityInterface
    {
        $this->packages[] = $package;

        return $this;
    }

    /**
     * Sets correct input format
     */
    public function setShippingDateTime(\DateTime $dateTime): ApiEntityInterface
    {
        $this->shippingDateTime = $dateTime->format('Y-m-d\TH:i:s');

        return $this;
    }
}
