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

    /**
     * In-app/legacy numeric codes some callers still send (e.g. the price
     * calculator). These are the historical service numbers carried over from
     * the v3 era — NOT necessarily the codes the current Shipping Guide v2
     * endpoint prices by. Use {@see shippingGuideCode()} when building a
     * Shipping Guide request; the two diverge for Express Nordic 09:00
     * (legacy 4850 vs Shipping Guide 0335).
     */
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

    /**
     * Product identifier as the Shipping Guide v2 endpoints
     * (/shippingguide/v2/products[/price]) expect it in the `product` query
     * parameter.
     *
     * Shipping Guide v2 identifies products by Bring's numeric service codes
     * (e.g. "5800" Pickup Parcel, "5600" Home Delivery, "5000" Business Parcel,
     * "4850" Business Parcel Express / "Pakke til bedrift ekspress"). It does
     * NOT price the v2 string names (EXPRESS_NORDIC_0900, …) — sending those
     * returns an empty product list ("no price"). Codes are returned as strings
     * so any future leading-zero code survives intact.
     *
     * Returns null for products whose Shipping Guide code is not yet confirmed;
     * callers should fall back to the enum string value for those so unknown
     * products are no worse off than before.
     *
     * NOTE on the EXPRESS_NORDIC_0900 case: despite the enum name, this catalog
     * uses it for Business Parcel Express (4850) — the everyday express parcel,
     * confirmed against Bring's revised-services list (5000/5100/5300/5600/5800/
     * 4850). It is NOT Bring's separate time-definite courier product "Express
     * Nordic 09:00" (0335), which is being decommissioned 2026-09-01.
     */
    public function shippingGuideCode(): ?string
    {
        return match ($this) {
            self::PICKUP_PARCEL => '5800',
            self::HOME_DELIVERY_PARCEL => '5600',
            self::BUSINESS_PARCEL => '5000',
            self::EXPRESS_NORDIC_0900 => '4850',
            self::MAILBOX_PARCEL => '3584',
            self::MAILBOX_PARCEL_TRACKED => '3570',
            default => null,
        };
    }

    /**
     * Product identifier as Bring's Booking API expects it in product.id.
     *
     * The Booking API takes numeric service codes (e.g. "5000" for
     * BUSINESS_PARCEL, "9000" for BUSINESS_PARCEL_RETURN) — the same
     * codes the v3 SDK used. Sending the v2 Shipping-Guide string name
     * (BUSINESS_PARCEL, …) makes Bring's gateway look it up in the
     * international catalog and reject Norway→Norway routes with
     * BOOK-INPUT-025 / BOOK_VALIDATION-014 ("product not available
     * between the given countries" / "customs declarations required for
     * exporting from Norway"), even though the parties are both NO.
     *
     * The mapping below covers the products with codes confirmed against
     * the v3 SDK catalog Bring still accepts in v4. Products NOT in this
     * match (e.g. HOME_DELIVERY_PARCEL, BUSINESS_PALLET, the home/express
     * return variants) fall back to the enum string value — Bring
     * accepts strings for some products and the fallback keeps unknown
     * cases at least as functional as today. If you hit BOOK-INPUT-025
     * on a product handled by the fallback, look up its numeric code in
     * Mybring (Customer numbers → service product line) and add it
     * here. Do not guess: a wrong numeric code silently books the wrong
     * product, while the string fallback at worst surfaces a 4xx.
     */
    public function bookingProductId(): string
    {
        $numeric = match ($this) {
            self::PICKUP_PARCEL => 5800,
            self::PICKUP_PARCEL_BULK => 5802,
            self::BUSINESS_PARCEL => 5000,
            self::BUSINESS_PARCEL_BULK => 5100,
            self::EXPRESS_NORDIC_0900 => 4850,
            self::MAILBOX_PARCEL => 3584,
            self::MAILBOX_PARCEL_TRACKED => 3570,
            self::CARGO_GROUPAGE => 5300,
            self::RETURN_PICKUP_PARCEL => 9300,
            self::RETURN_BUSINESS_PARCEL => 9000,
            self::RETURN_BUSINESS_PALLET => 9100,
            default => null,
        };

        return $numeric === null ? $this->value : (string) $numeric;
    }
}
