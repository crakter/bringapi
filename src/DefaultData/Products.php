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

    const PAKKE_TIL_HENTESTED = 5800;
    const RETUR_FRA_HENTESTED = 9300;
    const PAKKE_TIL_BEDRIFT = 5000;
    const RETUR_PAKKE_FRA_BEDIFT = 9000;
    const EKSPRESS_NESTE_DAG = 4850;
    const RETUREKSPRESS = 9600;
	const STYKKGODS_TIL_BEDIFT = 5300;
	const RETUR_STYKKGODS_FRA_BEDIFT = 9100;
	const SERVICEPAKKE = 'SERVICEPAKKE'; // @deprecated
    const SERVICEPAKKE_RETURSERVICE = 'SERVICEPAKKE_RETURSERVICE'; // @deprecated
    const BPAKKE_DOR_DOR = 'BPAKKE_DOR-DOR'; // @deprecated
    const BPAKKE_DOR_DOR_RETURSERVICE = 'BPAKKE_DOR-DOR_RETURSERVICE'; // @deprecated
    const EKSPRESS09 = 'EKSPRESS09'; // @deprecated
    const EKSPRESS09_RETURSERVICE = 'EKSPRESS09_RETURSERVICE'; // @deprecated
    const PICKUP_PARCEL = 'PICKUP_PARCEL'; // @deprecated
    const PICKUP_PARCEL_BULK = 'PICKUP_PARCEL_BULK'; // @deprecated
    const HOME_DELIVERY_PARCEL = 'HOME_DELIVERY_PARCEL'; // @deprecated
    const BUSINESS_PARCEL = 'BUSINESS_PARCEL'; // @deprecated
    const BUSINESS_PARCEL_BULK = 'BUSINESS_PARCEL_BULK'; // @deprecated
    const EXPRESS_NORDIC_0900_BULK = 'EXPRESS_NORDIC_0900_BULK'; // @deprecated
    const BUSINESS_PALLET = 'BUSINESS_PALLET'; // @deprecated
    const BUSINESS_PARCEL_HALFPALLET = 'BUSINESS_PARCEL_HALFPALLET'; // @deprecated
    const BUSINESS_PARCEL_QUARTERPALLET = 'BUSINESS_PARCEL_QUARTERPALLET'; // @deprecated
    const EXPRESS_NORDIC_0900 = 'EXPRESS_NORDIC_0900'; // @deprecated
    const PA_DOREN = 'PA_DOREN'; // @deprecated
    const MINIPAKKE = 'MINIPAKKE'; // @deprecated
    const A_POST = 'A-POST'; // @deprecated
    const B_POST = 'B-POST'; // @deprecated
    const SMAAPAKKER_A_POST = 'SMAAPAKKER_A-POST'; // @deprecated
    const SMAAPAKKER_B_POST = 'SMAAPAKKER_B-POST'; // @deprecated
    const EXPRESS_NORDIC_SAME_DAY = 'EXPRESS_NORDIC_SAME_DAY'; // @deprecated
    const EXPRESS_INTERNATIONAL_0900 = 'EXPRESS_INTERNATIONAL_0900'; // @deprecated
    const EXPRESS_INTERNATIONAL_1200 = 'EXPRESS_INTERNATIONAL_1200'; // @deprecated
    const EXPRESS_INTERNATIONAL = 'EXPRESS_INTERNATIONAL'; // @deprecated
    const EXPRESS_ECONOMY = 'EXPRESS_ECONOMY'; // @deprecated
    const CARGO_GROUPAGE = 'CARGO_GROUPAGE'; // @deprecated
    const COURIER_VIP = 'COURIER_VIP'; // @deprecated
    const COURIER_1H = 'COURIER_1H'; // @deprecated
    const COURIER_2H = 'COURIER_2H'; // @deprecated
    const COURIER_4H = 'COURIER_4H'; // @deprecated
    const COURIER_6H = 'COURIER_6H'; // @deprecated
    const OIL_EXPRESS = 'OIL_EXPRESS'; // @deprecated
    const MAILBOX_PARCEL_NO_TRACKING = 3584; // new PAKKE_I_POSTKASSEN from 13.01.2020
    const MAILBOX_PARCEL_RF_TRACKING = 3570; // new PAKKE_I_POSTKASSEN_SPORBAR from 13.01.2020
}
