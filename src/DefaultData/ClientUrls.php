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
 * BringApi ClientUrls
 *
 * Specify which client urls are available by Bring Api
 *
 * Quick example: <code>ClientUrls::REPORTS_LIST_AVAILABLE_CUSTOMERS</code>
 *
 * @author Martin Madsen <crakter@gmail.com>
 */
abstract class ClientUrls
{
    use \Crakter\BringApi\Traits\Validate;

    /**
     * Links for Reports API
     */
    // %s is returnFileType.
    const REPORTS_LIST_AVAILABLE_CUSTOMERS = 'https://www.mybring.com/reports/api/generate.%s';
    // 1st %s is CustomerNumber & 2nd %s is returnFileType.
    const REPORTS_LIST_AVAILABLE_REPORTS_CUSTOMER = 'https://www.mybring.com/reports/api/generate/%s.%s';
    // 1st %s is CustomerNumber & 2nd %s is reportTypeId & 3rd %s is returnFileType.
    const REPORTS_GENERATE = 'https://www.mybring.com/reports/api/generate/%s/%s.%s';
    // 1st %s is reportTypeId & 2rd %s is returnFileType.
    const REPORTS_CHECK_STATUS_OF_REPORT = 'https://www.mybring.com/reports/api/report/%s/status.%s';
    // 1st %s is reportTypeId & 2rd %s is returnFileType.
    const REPORTS_GET_REPORT = 'https://www.mybring.com/reports/api/report/%s.%s';
    // 1st %s is CustomerNumber or GroupId & 2rd %s is returnFileType.
    const REPORTS_LIST_INVOICE_NUMBERS = 'https://www.mybring.com/reports/api/invoices/%s.%s';

    /**
     * Links for Tracking Api
     */
    const TRACKING_NOT_LOGGED_IN = 'https://tracking.bring.com/tracking.%s';
    const TRACKING_LOGGED_IN = 'https://www.mybring.com/tracking/api/tracking.%s';
    const TRACKING_SIGNATURE = 'https://www.mybring.com/tracking/%s';

    /**
     * Link for Postal Code
     */
    const POSTALCODE_API = 'https://api.bring.com/shippingguide/api/postalCode.%s';

    /**
     * Links for Shipping Guide
     */
    const SHIPPINGGUIDE_SHIPMENT_PRICES = 'https://api.bring.com/shippingguide/v2/products/price';
    const SHIPPINGGUIDE_SHIPMENT_DELIVERY_TIME = 'https://api.bring.com/shippingguide/v2/products/expectedDelivery';
    const SHIPPINGGUIDE_SHIPMENT_ALL = 'https://api.bring.com/shippingguide/v2/products';

    /**
     * Links for Booking API
     */
    const BOOKING_LIST_CUSTOMER_NUMBERS = 'https://api.bring.com/booking/api/customers.%s';
    const BOOKING_BOOK_SHIPMENTS = 'https://api.bring.com/booking/api/booking';
    const BOOKING_ORDER_PICKUPS = 'https://api.bring.com/booking/api/pickupOrder';
}
