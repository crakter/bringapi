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

    public const PAKKE_TIL_HENTESTED = 5800;
    public const RETUR_FRA_HENTESTED = 9300;
    public const PAKKE_TIL_BEDRIFT = 5000;
    public const RETUR_PAKKE_FRA_BEDRIFT = 9000;
    public const EKSPRESS_NESTE_DAG = 4850;
    public const RETUREKSPRESS = 9600;
	public const STYKKGODS_TIL_BEDRIFT = 5300;
	public const RETUR_STYKKGODS_FRA_BEDRIFT = 9100;
	public const SERVICEPAKKE = 'SERVICEPAKKE'; // @deprecated
    public const SERVICEPAKKE_RETURSERVICE = 'SERVICEPAKKE_RETURSERVICE'; // @deprecated
    public const BPAKKE_DOR_DOR = 'BPAKKE_DOR-DOR'; // @deprecated
    public const BPAKKE_DOR_DOR_RETURSERVICE = 'BPAKKE_DOR-DOR_RETURSERVICE'; // @deprecated
    public const EKSPRESS09 = 'EKSPRESS09'; // @deprecated
    public const EKSPRESS09_RETURSERVICE = 'EKSPRESS09_RETURSERVICE'; // @deprecated
    public const PICKUP_PARCEL = 'PICKUP_PARCEL'; // @deprecated
    public const PICKUP_PARCEL_BULK = 'PICKUP_PARCEL_BULK'; // @deprecated
    public const HOME_DELIVERY_PARCEL = 'HOME_DELIVERY_PARCEL'; // @deprecated
    public const BUSINESS_PARCEL = 'BUSINESS_PARCEL'; // @deprecated
    public const BUSINESS_PARCEL_BULK = 'BUSINESS_PARCEL_BULK'; // @deprecated
    public const EXPRESS_NORDIC_0900_BULK = 'EXPRESS_NORDIC_0900_BULK'; // @deprecated
    public const BUSINESS_PALLET = 'BUSINESS_PALLET'; // @deprecated
    public const BUSINESS_PARCEL_HALFPALLET = 'BUSINESS_PARCEL_HALFPALLET'; // @deprecated
    public const BUSINESS_PARCEL_QUARTERPALLET = 'BUSINESS_PARCEL_QUARTERPALLET'; // @deprecated
    public const EXPRESS_NORDIC_0900 = 'EXPRESS_NORDIC_0900'; // @deprecated
    public const PA_DOREN = 'PA_DOREN'; // @deprecated
    public const MINIPAKKE = 'MINIPAKKE'; // @deprecated
    public const A_POST = 'A-POST'; // @deprecated
    public const B_POST = 'B-POST'; // @deprecated
    public const SMAAPAKKER_A_POST = 'SMAAPAKKER_A-POST'; // @deprecated
    public const SMAAPAKKER_B_POST = 'SMAAPAKKER_B-POST'; // @deprecated
    public const EXPRESS_NORDIC_SAME_DAY = 'EXPRESS_NORDIC_SAME_DAY'; // @deprecated
    public const EXPRESS_INTERNATIONAL_0900 = 'EXPRESS_INTERNATIONAL_0900'; // @deprecated
    public const EXPRESS_INTERNATIONAL_1200 = 'EXPRESS_INTERNATIONAL_1200'; // @deprecated
    public const EXPRESS_INTERNATIONAL = 'EXPRESS_INTERNATIONAL'; // @deprecated
    public const EXPRESS_ECONOMY = 'EXPRESS_ECONOMY'; // @deprecated
    public const CARGO_GROUPAGE = 'CARGO_GROUPAGE'; // @deprecated
    public const COURIER_VIP = 'COURIER_VIP'; // @deprecated
    public const COURIER_1H = 'COURIER_1H'; // @deprecated
    public const COURIER_2H = 'COURIER_2H'; // @deprecated
    public const COURIER_4H = 'COURIER_4H'; // @deprecated
    public const COURIER_6H = 'COURIER_6H'; // @deprecated
    public const OIL_EXPRESS = 'OIL_EXPRESS'; // @deprecated
    public const MAILBOX_PARCEL_NO_TRACKING = 3584; // new PAKKE_I_POSTKASSEN from 13.01.2020
    public const MAILBOX_PARCEL_RF_TRACKING = 3570; // new PAKKE_I_POSTKASSEN_SPORBAR from 13.01.2020
}
