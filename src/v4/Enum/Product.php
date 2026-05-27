<?php

declare(strict_types=1);

namespace Bring\Api\Enum;

/**
 * Bring product codes (current Shipping Guide v2 + Booking API names).
 *
 * The list below covers the products documented at
 * https://developer.bring.com/api/products/ as of late 2025. Many of the
 * legacy string-style codes (BPAKKE_DOR-DOR, SERVICEPAKKE, …) are intentionally
 * absent — they were retired and Bring rejects them in v2.
 */
enum Product: string
{
    // Parcel
    case PICKUP_PARCEL = 'PICKUP_PARCEL';
    case PICKUP_PARCEL_BULK = 'PICKUP_PARCEL_BULK';
    case HOME_DELIVERY_PARCEL = 'HOME_DELIVERY_PARCEL';
    case BUSINESS_PARCEL = 'BUSINESS_PARCEL';
    case BUSINESS_PARCEL_BULK = 'BUSINESS_PARCEL_BULK';
    case MAILBOX_PARCEL = 'MAILBOX_PARCEL';
    case MAILBOX_PARCEL_TRACKED = 'MAILBOX_PARCEL_TRACKED';

    // Express
    case EXPRESS_NORDIC_SAME_DAY = 'EXPRESS_NORDIC_SAME_DAY';
    case EXPRESS_NORDIC_0900 = 'EXPRESS_NORDIC_0900';
    case EXPRESS_NORDIC_0900_BULK = 'EXPRESS_NORDIC_0900_BULK';
    case EXPRESS_INTERNATIONAL_0900 = 'EXPRESS_INTERNATIONAL_0900';
    case EXPRESS_INTERNATIONAL_1200 = 'EXPRESS_INTERNATIONAL_1200';
    case EXPRESS_INTERNATIONAL = 'EXPRESS_INTERNATIONAL';
    case EXPRESS_ECONOMY = 'EXPRESS_ECONOMY';

    // Pallet / cargo
    case BUSINESS_PALLET = 'BUSINESS_PALLET';
    case BUSINESS_PARCEL_HALFPALLET = 'BUSINESS_PARCEL_HALFPALLET';
    case BUSINESS_PARCEL_QUARTERPALLET = 'BUSINESS_PARCEL_QUARTERPALLET';
    case CARGO_GROUPAGE = 'CARGO_GROUPAGE';

    // Returns
    case RETURN_PICKUP_PARCEL = 'PICKUP_PARCEL_RETURN';
    case RETURN_HOME_DELIVERY_PARCEL = 'HOME_DELIVERY_PARCEL_RETURN';
    case RETURN_BUSINESS_PARCEL = 'BUSINESS_PARCEL_RETURN';
    case RETURN_BUSINESS_PALLET = 'BUSINESS_PALLET_RETURN';

    /** Numeric legacy codes still accepted by some endpoints. */
    public function legacyNumericCode(): ?int
    {
        return match ($this) {
            self::PICKUP_PARCEL => 5800,
            self::BUSINESS_PARCEL => 5000,
            self::EXPRESS_NORDIC_0900 => 4850,
            self::MAILBOX_PARCEL => 3584,
            self::MAILBOX_PARCEL_TRACKED => 3570,
            default => null,
        };
    }
}
