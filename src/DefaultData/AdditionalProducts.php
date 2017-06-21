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
 * BringApi AdditionalProducts
 *
 * Specify which additonal products are available by Bring Api
 *
 * Quick example: <code>AdditionalProducts::COD</code>
 *
 * @author Martin Madsen <crakter@gmail.com>
 */
abstract class AdditionalProducts
{
    use \Crakter\BringApi\Traits\Validate;

    const ENOTIFICATION = 'EVARSLING';
    const COD = 'POSTOPPKRAV';
    const SATURDAYDELIVERY = 'LORDAGSUTKJORING';
    const ENVELOPE = 'ENVELOPE';
    const ADVISEMENT = 'ADVISERING';
    const PICKUP_POINT = 'PICKUP_POINT';
    const EVE_DELIVERY = 'EVE_DELIVERY';

    const ALLOWEDMETHOD = [
        'EVARSLING' => [
            'BPAKKE_DOR-DOR',
            'SERVICEPAKKE',
            'EKSPRESS09',
        ],
        'POSTOPPKRAV' => [
            'A-POST',
            'B-POST',
            'BPAKKE_DOR-DOR',
            'SERVICEPAKKE',
            'PA_DOREN',
            'EKSPRESS09',
        ],
        'LORDAGSUTKJORING' => [
            'EKSPRESS09',
        ],
        'ENVELOPE' => [
            'EXPRESS_INTERNATIONAL_0900',
            'EXPRESS_INTERNATIONAL_1200',
            'EXPRESS_INTERNATIONAL',
        ],
        'ADVISERING' => [
            'CARGO_GROUPAGE',
        ],
        'PICKUP_POINT' => [
            'PICKUP_PARCEL',
            'PICKUP_PARCEL_BULK',
        ],
        'EVE_DELIVERY' => [
            'CARGO',
            'CARGO_GROUPAGE',
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
