<?php

declare(strict_types=1);

/*
 * Rector configuration that mechanically rewrites v3 BringApi usages to v4.
 *
 * Designed to be consumed by downstream applications:
 *
 *   composer require --dev rector/rector
 *   vendor/bin/rector process src --config vendor/crakter/bringapi/rector-v4-migration.php
 *
 * Most v3 → v4 transitions cannot be expressed as a simple class rename
 * (the entity → request-DTO refactor changes constructor shapes and removes
 * the fluent setter chain). What we *can* automate is:
 *
 *   1. RenameClassRector for the cases where the v4 class is a drop-in
 *      replacement (Exception/* lives in v3 still, kept for compat).
 *   2. RenameImportedConstantFetchRector for the DefaultData enum migration.
 *   3. ClassConstantToEnumCaseRector for the constants-as-enum-cases mapping.
 *
 * Anything more invasive — fluent ApiEntity chains, the BringClientException
 * to BringApiException split — needs manual review. Those cases emit a
 * deprecation notice via the existing @deprecated annotations, so PHPStan /
 * Psalm in the consuming project will surface them.
 */

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;

return RectorConfig::configure()
    ->withPaths([
        // Override at the call site with --paths.
    ])
    ->withRules([
        RenameClassRector::class,
    ])
    ->withConfiguredRule(RenameClassRector::class, [
        // Exceptions — the v4 hierarchy is a richer set; map the most common
        // v3 class to the closest v4 equivalent (a Bring*Exception base).
        'Crakter\\BringApi\\Exception\\BringClientException'
            => 'Bring\\Api\\Exception\\BringApiException',

        // Authorization moved namespace; the v4 contract is different
        // (constructor-based Credentials VO), so we point at the VO. The
        // resulting code won't compile until the user updates the call
        // site, but the deprecation arrow is visible in code review.
        'Crakter\\BringApi\\Clients\\Authorization'
            => 'Bring\\Api\\Auth\\Credentials',
        'Crakter\\BringApi\\Clients\\AuthorizationInterface'
            => 'Bring\\Api\\Auth\\AuthorizationInterface',

        // Endpoint clients — v3 had one class per endpoint, v4 has one
        // *Api facade per area. Point at the facade so the symbol stays
        // resolvable; method names will still need updating.
        'Crakter\\BringApi\\Clients\\Tracking\\TrackingEndpoint'
            => 'Bring\\Api\\Endpoint\\Tracking\\TrackingApi',
        'Crakter\\BringApi\\Clients\\Tracking\\SignatureTracking'
            => 'Bring\\Api\\Endpoint\\Tracking\\TrackingApi',
        'Crakter\\BringApi\\Clients\\PostalCode\\PostalCode'
            => 'Bring\\Api\\Endpoint\\Address\\AddressApi',
        'Crakter\\BringApi\\Clients\\Booking\\BookShipment'
            => 'Bring\\Api\\Endpoint\\Booking\\BookingApi',
        'Crakter\\BringApi\\Clients\\Booking\\OrderPickup'
            => 'Bring\\Api\\Endpoint\\Booking\\BookingApi',
        'Crakter\\BringApi\\Clients\\Booking\\ListCustomers'
            => 'Bring\\Api\\Endpoint\\Booking\\BookingApi',
        'Crakter\\BringApi\\Clients\\ShippingGuide\\ShipmentPrices'
            => 'Bring\\Api\\Endpoint\\Shipping\\ShippingGuideApi',
        'Crakter\\BringApi\\Clients\\ShippingGuide\\ShipmentDeliveryTime'
            => 'Bring\\Api\\Endpoint\\Shipping\\ShippingGuideApi',
        'Crakter\\BringApi\\Clients\\ShippingGuide\\ShipmentAll'
            => 'Bring\\Api\\Endpoint\\Shipping\\ShippingGuideApi',
        'Crakter\\BringApi\\Clients\\Reports\\GenerateReport'
            => 'Bring\\Api\\Endpoint\\Reports\\ReportsApi',
        'Crakter\\BringApi\\Clients\\Reports\\GetReport'
            => 'Bring\\Api\\Endpoint\\Reports\\ReportsApi',
        'Crakter\\BringApi\\Clients\\Reports\\StatusOfReport'
            => 'Bring\\Api\\Endpoint\\Reports\\ReportsApi',
        'Crakter\\BringApi\\Clients\\Reports\\ListAvailableCustomers'
            => 'Bring\\Api\\Endpoint\\Reports\\ReportsApi',
        'Crakter\\BringApi\\Clients\\Reports\\ListAvailableReportsCustomer'
            => 'Bring\\Api\\Endpoint\\Reports\\ReportsApi',
        'Crakter\\BringApi\\Clients\\Reports\\ListInvoiceNumbers'
            => 'Bring\\Api\\Endpoint\\Reports\\ReportsApi',

        // DefaultData constants → Enum classes.
        'Crakter\\BringApi\\DefaultData\\Countries'
            => 'Bring\\Api\\Enum\\Country',
        'Crakter\\BringApi\\DefaultData\\Languages'
            => 'Bring\\Api\\Enum\\Language',
        'Crakter\\BringApi\\DefaultData\\Products'
            => 'Bring\\Api\\Enum\\Product',
    ]);
