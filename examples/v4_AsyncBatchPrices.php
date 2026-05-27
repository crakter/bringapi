<?php

declare(strict_types=1);

/*
 * Bring API v4 — fan out shipping-guide price requests concurrently.
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Bring\Api\ApiClient;
use Bring\Api\Auth\Credentials;
use Bring\Api\Endpoint\Shipping\PriceEndpoint;
use Bring\Api\Endpoint\Shipping\PriceRequest;
use Bring\Api\Enum\Country;
use Bring\Api\Enum\Product;
use Bring\Api\Exception\BringException;

$bring = ApiClient::withCredentials(new Credentials(
    uid: getenv('BRING_UID') ?: 'me@example.com',
    apiKey: getenv('BRING_API_KEY') ?: 'demo-key',
    clientUrl: 'https://example.com',
));

// Three carts shipping from Oslo to different cities, priced in parallel.
$destinations = ['5003' => 'Bergen', '7010' => 'Trondheim', '9008' => 'Tromsø'];

$endpoints = [];
foreach ($destinations as $postalCode => $_city) {
    $endpoints[$postalCode] = new PriceEndpoint(new PriceRequest(
        fromCountry: Country::NO,
        fromPostalCode: '0150',
        toCountry: Country::NO,
        toPostalCode: $postalCode,
        packages: [['weightInGrams' => 2000]],
        products: [Product::HOME_DELIVERY_PARCEL],
    ));
}

try {
    $results = $bring->async()->settleAll($endpoints)->wait();
    foreach ($results as $postalCode => $entry) {
        if ($entry['state'] === 'fulfilled') {
            $price = $entry['value']->products[0]->priceWithVat ?? 0.0;
            printf("%s → %s : %.2f NOK\n", $postalCode, $destinations[$postalCode], $price);
        } else {
            printf("%s → %s : FAILED (%s)\n", $postalCode, $destinations[$postalCode], $entry['reason']->getMessage());
        }
    }
} catch (BringException $e) {
    fprintf(STDERR, "Bring error: %s\n", $e->getMessage());
    exit(1);
}
