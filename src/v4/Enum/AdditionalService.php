<?php

declare(strict_types=1);

namespace Bring\Api\Enum;

/**
 * Common additional-service codes accepted by Shipping Guide v2 and Booking.
 * The full list is product-specific; consult
 * https://developer.bring.com/api/products/#additional-services.
 */
enum AdditionalService: string
{
    case EVARSLING = 'EVARSLING';
    case POSTOPPKRAV = 'POSTOPPKRAV';
    case SOCIAL_CONTROL = 'SOCIAL_CONTROL';
    case INSURANCE = 'INSURANCE';
    case OPPDAGT = 'OPPDAGT';
    case FRAGTBREVS_KOPI = 'FRAGTBREVS_KOPI';
    case SIGNATURE_REQUIRED = 'SIGNATURE_REQUIRED';
    case DELIVERY_ON_FLOOR = 'DELIVERY_ON_FLOOR';
    case INDOOR_DELIVERY = 'INDOOR_DELIVERY';
    case SIMPLIFIED_DELIVERY = 'SIMPLIFIED_DELIVERY';
    case FLEX_DELIVERY = 'FLEX_DELIVERY';
    case PICK_UP_AT_TERMINAL = 'PICK_UP_AT_TERMINAL';
    case INCLUDED_HANDLING_ADR = 'INCLUDED_HANDLING_ADR';
}
