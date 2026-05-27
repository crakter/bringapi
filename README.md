# Bring API PHP

[![CI](https://github.com/crakter/bringapi/actions/workflows/ci.yml/badge.svg)](https://github.com/crakter/bringapi/actions/workflows/ci.yml)

A PHP client library for [Bring's developer APIs](https://developer.bring.com/api/):
Shipping Guide, Booking, Tracking, Reports, Postal Code, the new Address API,
Pickup Point, Modify Delivery, and Order Management (REST).

Used in production by a large Norwegian wholesaler.

## Install

```sh
composer require crakter/bringapi
```

## Requirements

- PHP **8.2** or newer
- A PSR-18 HTTP client (Guzzle 7 is the suggested default)
- `simplexml` extension (built-in on most distributions)
- `phpoffice/phpspreadsheet` *only* if you call Reports endpoints that return
  XLS

## Supported APIs

| API | Coverage | Bring docs |
|-----|----------|------------|
| Shipping Guide (v2) | price / delivery time / products | [link](https://developer.bring.com/api/shipping-guide/) |
| Booking | book, pickup order, customers | [link](https://developer.bring.com/api/booking/) |
| Tracking | track, signature image | [link](https://developer.bring.com/api/tracking/) |
| Reports | list, generate, status, download, invoices | [link](https://developer.bring.com/api/reports/) |
| Postal Code (legacy) | single lookup | [link](https://developer.bring.com/api/postal-code/) |
| **Address (new)** | postal-code lookup, suggestions, mailbox-delivery dates | [link](https://developer.bring.com/api/postal-code/) |
| **Pickup Point** | all / by id / by postal code / by location (NO/SE/DK/FI only) | [link](https://developer.bring.com/api/pickup-point/) |
| **Modify Delivery** | stop, change address, update contact (NO/SE/DK only) | [link](https://developer.bring.com/api/modify-delivery/) |
| **Order Management (REST)** | get order, packaging list | [link](https://developer.bring.com/api/order-management/) |

The SOAP variant of Order Management is intentionally out of scope.

## Quick start

```php
use Bring\Api\ApiClient;
use Bring\Api\Auth\Credentials;
use Bring\Api\Enum\Country;

$bring = ApiClient::withCredentials(new Credentials(
    uid: 'me@example.com',
    apiKey: getenv('BRING_API_KEY'),
    clientUrl: 'https://example.com',
));

// Modern address lookup
$result = $bring->address()->postalCode(Country::NO, '0150');
echo $result->city; // "OSLO"

// Pickup points near a postal code
foreach ($bring->pickupPoint()->byPostalCode(Country::NO, '0150')->pickupPoints as $pp) {
    echo "{$pp->name} — {$pp->address}\n";
}

// Tracking
$tracking = $bring->tracking()->track('TESTPACKAGE-AT-PICKUPPOINT');
echo $tracking->latestEvent()?->description;
```

## Test mode

Calls that support `X-Bring-Test-Indicator` (Booking, Modify Delivery) are
toggled at the facade:

```php
$bring = ApiClient::withCredentials($creds)->withTestMode(true);
// Every request now carries X-Bring-Test-Indicator: true
```

## Booking a shipment

```php
use Bring\Api\Dto\{Address, Contact, Dimensions, Package};
use Bring\Api\Endpoint\Booking\BookingRequest;
use Bring\Api\Enum\{Country, Product};

$request = BookingRequest::single(
    schemaVersion: '1',
    customerNumber: 'PARCELS_NORWAY-10001234567',
    product: Product::HOME_DELIVERY_PARCEL,
    sender: new Address(
        name: 'Acme AS', addressLine: 'Sandakerveien 24c', addressLine2: null,
        postalCode: '0473', city: 'Oslo', countryCode: Country::NO,
        contact: new Contact(name: 'Pickup Person', phoneNumber: '+4799999999'),
    ),
    recipient: new Address(
        name: 'John Doe', addressLine: 'Storgata 1', addressLine2: null,
        postalCode: '5003', city: 'Bergen', countryCode: Country::NO,
    ),
    packages: [new Package(
        weightInKg: 2,
        dimensions: new Dimensions(lengthInCm: 30, widthInCm: 20, heightInCm: 15),
    )],
);

$resp = $bring->booking()->book($request);
foreach ($resp->consignments as $c) {
    echo "Consignment {$c->confirmation}\n";
}
```

## Credentials and logging

`Credentials` wraps the API key behind `#[\SensitiveParameter]` (PHP 8.2+
scrubs it from stack traces) and masks it in `print_r` / `var_dump` output —
debug dumps only show a SHA-256 fingerprint.

Pass any PSR-3 logger to `ApiClient::withCredentials()` and it is automatically
wrapped in a `RedactingLogger` that strips `X-Mybring-*` headers and the raw
API key from every log line:

```php
$bring = ApiClient::withCredentials($creds, logger: $monolog);
```

`BringApiException` never embeds the raw response body in `getMessage()` —
Bring occasionally echoes credentials in error envelopes. Callers that want
the response body can call `BringApiException::getResponse()` explicitly.

## Error handling

```php
use Bring\Api\Exception\{BringApiException, BringTransportException, BringException};

try {
    $bring->shippingGuide()->price($request);
} catch (BringApiException $e) {
    // Bring returned 4xx/5xx — parsed error codes in $e->getErrors()
    error_log("Bring rejected request (HTTP {$e->getStatusCode()})");
    foreach ($e->getErrors() as $err) {
        error_log("  {$err->code}: {$err->message}");
    }
} catch (BringTransportException $e) {
    // PSR-18 network failure (DNS, TLS, timeout); $e->getPrevious() has the cause
} catch (BringException $e) {
    // Catch-all for anything this library throws
}
```

## v3 → v4 migration

See [UPGRADE-4.0.md](UPGRADE-4.0.md) for the full mapping. v3 classes
(`Crakter\BringApi\*`) still ship and still work — they are marked
`@deprecated` and will be removed in 5.0.

## Examples

Set credentials in your environment first:

```sh
export BRING_UID="me@example.com"
export BRING_API_KEY="1234abc-abcd-1234-5678-abcd1234abcd"
export BRING_CUSTOMER_NUMBER="PARCELS_NORWAY-10001123123"
```

Then run any example from the project root:

```sh
php examples/v4_PostalCode.php 0150
php examples/v4_PickupPoint.php 0150
php examples/v4_Tracking.php TESTPACKAGE-AT-PICKUPPOINT
php examples/v4_ShippingGuidePrice.php 0150 5003
php examples/v4_BookAndPickup.php   # uses test mode, no labels generated
```

## Development

```sh
composer install
vendor/bin/phpunit                              # all tests (legacy + v4)
vendor/bin/phpunit --testsuite v4               # v4 tests only
vendor/bin/phpstan analyse                      # static analysis on v4
vendor/bin/php-cs-fixer fix --dry-run --diff    # coding-style check
```

## License

MIT
