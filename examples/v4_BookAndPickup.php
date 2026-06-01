<?php

declare(strict_types=1);

/*
 * Bring API v4 — Book a shipment + order a pickup (TEST mode)
 *
 * Always runs against Bring's test indicator; no real labels are generated.
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Bring\Api\ApiClient;
use Bring\Api\Auth\Credentials;
use Bring\Api\Dto\Address;
use Bring\Api\Dto\Contact;
use Bring\Api\Dto\Dimensions;
use Bring\Api\Dto\Package;
use Bring\Api\Endpoint\Booking\BookingRequest;
use Bring\Api\Endpoint\Booking\PickupRequest;
use Bring\Api\Enum\Country;
use Bring\Api\Enum\Product;
use Bring\Api\Exception\BringException;

$bring = ApiClient::withCredentials(new Credentials(
    uid: getenv('BRING_UID') ?: 'me@example.com',
    apiKey: getenv('BRING_API_KEY') ?: 'demo-key',
    clientUrl: 'https://example.com',
))->withTestMode(true);

$customerNumber = getenv('BRING_CUSTOMER_NUMBER') ?: 'PARCELS_NORWAY-10001234567';

$sender = new Address(
    name: 'Acme AS',
    addressLine: 'Sandakerveien 24c',
    addressLine2: null,
    postalCode: '0473',
    city: 'Oslo',
    countryCode: Country::NO,
    contact: new Contact(name: 'Pickup Person', phoneNumber: '+4799999999'),
);

$recipient = new Address(
    name: 'John Doe',
    addressLine: 'Storgata 1',
    addressLine2: null,
    postalCode: '5003',
    city: 'Bergen',
    countryCode: Country::NO,
    contact: new Contact(name: 'John Doe', email: 'john@example.com'),
);

$booking = BookingRequest::single(
    schemaVersion: '1',
    customerNumber: $customerNumber,
    product: Product::HOME_DELIVERY_PARCEL,
    sender: $sender,
    recipient: $recipient,
    packages: [
        new Package(
            weightInKg: 2,
            dimensions: new Dimensions(lengthInCm: 30, widthInCm: 20, heightInCm: 15),
            goodsDescription: 'Test parcel',
        ),
    ],
);

try {
    $resp = $bring->booking()->book($booking);
    foreach ($resp->consignments as $c) {
        printf("Booked consignment %s\n", $c->confirmation ?? '(no number)');
    }

    $pickup = new PickupRequest(
        customerNumber: $customerNumber,
        pickupAddress: $sender,
        readyAt: new DateTimeImmutable('+1 day 09:00'),
        closingAt: new DateTimeImmutable('+1 day 17:00'),
        numberOfPackages: 1,
        totalWeightInKg: 2,
    );

    $pickupResp = $bring->booking()->pickup($pickup);
    printf("Pickup confirmation: %s\n", $pickupResp->confirmationNumber ?? '(none)');
} catch (BringException $e) {
    fprintf(STDERR, "Bring error: %s\n", $e->getMessage());
    exit(1);
}
