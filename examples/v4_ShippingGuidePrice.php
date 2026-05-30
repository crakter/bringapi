<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Bring\Api\ApiClient;
use Bring\Api\Auth\Credentials;
use Bring\Api\Endpoint\Shipping\PriceRequest;
use Bring\Api\Enum\Country;
use Bring\Api\Enum\Product;
use Bring\Api\Exception\BringException;

$from = $argv[1] ?? '0150';
$to = $argv[2] ?? '5003';

$bring = ApiClient::withCredentials(new Credentials(
    uid: getenv('BRING_UID') ?: 'me@example.com',
    apiKey: getenv('BRING_API_KEY') ?: 'demo-key',
    clientUrl: 'https://example.com',
));

$request = new PriceRequest(
    fromCountry: Country::NO,
    fromPostalCode: $from,
    toCountry: Country::NO,
    toPostalCode: $to,
    packages: [['weightInGrams' => 2000, 'length' => 30, 'width' => 20, 'height' => 15]],
    products: [Product::HOME_DELIVERY_PARCEL, Product::PICKUP_PARCEL],
    withExpectedDelivery: true,
);

try {
    $resp = $bring->shippingGuide()->price($request);
    foreach ($resp->products as $p) {
        printf(
            "%-30s  %7.2f %s  (delivery: %d days)\n",
            $p->productId,
            $p->priceWithVat ?? 0.0,
            $p->currency ?? 'NOK',
            $p->expectedDeliveryDays ?? 0,
        );
    }
} catch (BringException $e) {
    fprintf(STDERR, "Bring error: %s\n", $e->getMessage());
    exit(1);
}
