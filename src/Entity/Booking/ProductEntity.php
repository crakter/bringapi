<?php

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
use Crakter\BringApi\Exception\ApiEntityNotCorrectException;
use Crakter\BringApi\DefaultData\AllowedServices;

/**
 * BringApi PartiesEntity
 *
 * An class to supply correct information to Bring Api servers
 *
 * Quick setup: <code>$product = (new ProductEntity)
 *                     ->setId(Products::SERVICEPAKKE)
 *                     ->setCustomerNumber('PARCELS_NORWAY-10005540322');</code>
 *
 * @property array $services                Add services to product. <code></code>
 * @property array $customsDeclaration      Add customs declaration to product. <code></code>
 * @method ApiEntityInterface setId(string $string)
 * @method string getId()
 * @method ApiEntityInterface setCustomerNumber(string $string)
 * @method string getCustomerNumber()
 * @method ApiEntityInterface setServices(ApiEntityInterface $array)
 * @method array getServices()
 * @method ApiEntityInterface setCustomsDeclaration(array $array)
 * @method array getCustomsDeclaration()
 * @author Martin Madsen <crakter@gmail.com>
 */
class ProductEntity extends ApiEntityBase implements ApiEntityInterface
{
    /**
     * @var string $id          Product id. <code>Products::SERVICEPAKKE</code>
     */
    public $id;

    /**
     * @var string $customerNumber This needs to be set to the correct customerNumber of shipment. <code>'PARCELS_NORWAY-10005540322'</code>
     */
    public $customerNumber;

    public function setServices(ApiEntityInterface $services): ApiEntityInterface
    {
        if ($this->getId() == '') {
            throw new ApiEntityNotCorrectException('The Product Id needs to be set before the Services, to be able to check if allowed');
        }
        $services = $services->toArray();
        foreach ($services as $key => $var) {
            AllowedServices::hasAppliesTo($key, $this->getId());
        }
        $this->services = $services;

        return $this;
    }
}
