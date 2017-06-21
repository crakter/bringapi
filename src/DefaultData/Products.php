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

/**
 * BringApi Products
 *
 * Specify which products are available from Bring Api
 *
 * Quick example: <code>Products::SERVICEPAKKE</code>
 *
 * @author Martin Madsen <crakter@gmail.com>
 */
abstract class Products
{
    use \Crakter\BringApi\Traits\Validate;

    const SERVICEPAKKE = 'SERVICEPAKKE';
    const SERVICEPAKKE_RETURSERVICE = 'SERVICEPAKKE_RETURSERVICE';
    const BPAKKE_DOR_DOR = 'BPAKKE_DOR-DOR';
    const BPAKKE_DOR_DOR_RETURSERVICE = 'BPAKKE_DOR-DOR_RETURSERVICE';
    const EKSPRESS09 = 'EKSPRESS09';
    const EKSPRESS09_RETURSERVICE = 'EKSPRESS09_RETURSERVICE';
    const PICKUP_PARCEL = 'PICKUP_PARCEL';
    const PICKUP_PARCEL_BULK = 'PICKUP_PARCEL_BULK';
    const HOME_DELIVERY_PARCEL = 'HOME_DELIVERY_PARCEL';
    const BUSINESS_PARCEL = 'BUSINESS_PARCEL';
    const BUSINESS_PARCEL_BULK = 'BUSINESS_PARCEL_BULK';
    const EXPRESS_NORDIC_0900_BULK = 'EXPRESS_NORDIC_0900_BULK';
    const BUSINESS_PALLET = 'BUSINESS_PALLET';
    const BUSINESS_PARCEL_HALFPALLET = 'BUSINESS_PARCEL_HALFPALLET';
    const BUSINESS_PARCEL_QUARTERPALLET = 'BUSINESS_PARCEL_QUARTERPALLET';
    const EXPRESS_NORDIC_0900 = 'EXPRESS_NORDIC_0900';
    const PA_DOREN = 'PA_DOREN';
    const MINIPAKKE = 'MINIPAKKE';
    const A_POST = 'A-POST';
    const B_POST = 'B-POST';
    const SMAAPAKKER_A_POST = 'SMAAPAKKER_A-POST';
    const SMAAPAKKER_B_POST = 'SMAAPAKKER_B-POST';
    const EXPRESS_NORDIC_SAME_DAY = 'EXPRESS_NORDIC_SAME_DAY';
    const EXPRESS_INTERNATIONAL_0900 = 'EXPRESS_INTERNATIONAL_0900';
    const EXPRESS_INTERNATIONAL_1200 = 'EXPRESS_INTERNATIONAL_1200';
    const EXPRESS_INTERNATIONAL = 'EXPRESS_INTERNATIONAL';
    const EXPRESS_ECONOMY = 'EXPRESS_ECONOMY';
    const CARGO_GROUPAGE = 'CARGO_GROUPAGE';
    const COURIER_VIP = 'COURIER_VIP';
    const COURIER_1H = 'COURIER_1H';
    const COURIER_2H = 'COURIER_2H';
    const COURIER_4H = 'COURIER_4H';
    const COURIER_6H = 'COURIER_6H';
    const OIL_EXPRESS = 'OIL_EXPRESS';
}
