<?php

/*
 * This file is part of the BringApi package.
 *
 * (c) Martin Madsen <crakter@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Crakter\BringApi\DefaultData;

use Crakter\BringApi\Exception\ProductAppliesToNotAllowedException;
use ReflectionClass;

/**
 * BringApi AllowedServices
 *
 * Specify which additonal products are available by Bring Api
 *
 * Quick example: <code>AllowedServices::hasAppliesTo(Products::SERVICEPAKKE);</code>
 *
 * @author Martin Madsen <crakter@gmail.com>
 */
abstract class AllowedServices
{
    use \Crakter\BringApi\Traits\Validate;

    const ALLOWEDMETHOD = [
        'cashOnDelivery' => [
            'BPAKKE_DOR-DOR',
            'PICKUP_PARCEL',
            'PICKUP_PARCEL_BULK',
            'HOME_DELIVERY_PARCEL',
            'BUSINESS_PARCEL',
            'BUSINESS_PARCEL_BULK',
            'EXPRESS_NORDIC_0900_BULK',
        ],
        'recipientNotification' => [
            'SERVICEPAKKE',
            'BPAKKE_DOR-DOR',
            'PA_DOREN',
            'EKSPRESS09',
            'PICKUP_PARCEL',
            'PICKUP_PARCEL_BULK',
            'HOME_DELIVERY_PARCEL',
            'BUSINESS_PARCEL',
            'BUSINESS_PARCEL_BULK',
            'EXPRESS_NORDIC_0900_BULK',
            'BUSINESS_PALLET',
            'BUSINESS_PARCEL_HALFPALLET',
            'BUSINESS_PARCEL_QUARTERPALLET',
            'EXPRESS_NORDIC_0900',
        ],
        'socialControl' => [
            'SERVICEPAKKE',
        ],
        'simpleDelivery' => [
            'BPAKKE_DOR-DOR',
            'PA_DOREN',
        ],
        'deliveryOption' => [
            'BPAKKE_DOR-DOR',
            'PICKUP_PARCEL',
            'PICKUP_PARCEL_BULK',
        ],
        'saturdayDelivery' => [
            'EKSPRESS09',
        ],
        'FLEX_DELIVERY' => [
            'PICKUP_PARCEL',
            'PICKUP_PARCEL_BULK',
            'HOME_DELIVERY_PARCEL',
            'BUSINESS_PARCEL',
            'BUSINESS_PARCEL_BULK',
            'EXPRESS_NORDIC_0900_BULK',
            'BUSINESS_PALLET',
            'BUSINESS_PARCEL_HALFPALLET',
            'BUSINESS_PARCEL_QUARTERPALLET',
            'EXPRESS_NORDIC_0900',
        ],
        'phonenotification' => [
            'BUSINESS_PARCEL',
            'BUSINESS_PARCEL_BULK',
            'EXPRESS_NORDIC_0900_BULK',
            'BUSINESS_PALLET',
            'BUSINESS_PARCEL_HALFPALLET',
            'BUSINESS_PARCEL_QUARTERPALLET',
            'EXPRESS_NORDIC_0900',
        ],
        'deliveryIndoors' => [
            'BUSINESS_PARCEL',
            'BUSINESS_PARCEL_BULK',
            'EXPRESS_NORDIC_0900_BULK',
            'BUSINESS_PALLET',
            'BUSINESS_PARCEL_HALFPALLET',
            'BUSINESS_PARCEL_QUARTERPALLET',
            'EXPRESS_NORDIC_0900',
        ],
    ];

    /**
     * Check if Additonal Product is available for product
     * @param  string $name    name of additional product
     * @param  string $product name of product
     * @return bool   true/false
     */
    public static function hasAppliesTo(string $name, string $product): bool
    {
        $class = new ReflectionClass(__CLASS__);
        if (
            isset($class->getConstant('ALLOWEDMETHOD')[$name]) &&
            in_array($product, $class->getConstant('ALLOWEDMETHOD')[$name])
        ) {
            return true;
        }
        throw new ProductAppliesToNotAllowedException(
            sprintf('$name(%s) with $product(%s) is not allowed by Bring API in %s', $name, $product, $class->getName())
        );

        return false;
    }
}
