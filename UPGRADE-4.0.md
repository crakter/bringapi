# Upgrade from 3.x → 4.0

4.0 is a full rewrite with a typed, PSR-18-based API. The v3 namespaces
(`Crakter\BringApi\Clients\*`, `Crakter\BringApi\Entity\*`,
`Crakter\BringApi\DefaultData\*`) are still shipped and still work, but every
class in those namespaces is marked `@deprecated` and will be removed in 5.0.

The new code lives under `Bring\Api\*`.

## TL;DR

```php
// v3
use Crakter\BringApi\Clients\Authorization;
use Crakter\BringApi\Clients\PostalCode\PostalCode;
use Crakter\BringApi\Entity\PostalCodeEntity;

$auth = (new Authorization)
    ->setApiKey($key)
    ->setClientId('me@example.com')
    ->setClientUrl('https://example.com');

$entity = (new PostalCodeEntity)->setPnr('0150');
$result = (new PostalCode)
    ->setAuthorizationModule($auth)
    ->setApiEntity($entity)
    ->send()
    ->toArray();
```

```php
// v4
use Bring\Api\ApiClient;
use Bring\Api\Auth\Credentials;
use Bring\Api\Enum\Country;

$bring = ApiClient::withCredentials(
    new Credentials('me@example.com', $key, 'https://example.com'),
);

$result = $bring->address()->postalCode(Country::NO, '0150'); // typed DTO
echo $result->city;
```

## Why bother

- **Typed everything.** Request DTOs, response DTOs, enums for products /
  countries / formats. No more `array|null` returns.
- **PSR-18 transport.** Use any compliant HTTP client; Guzzle is the default.
- **Credentials never leak.** API key is `#[\SensitiveParameter]`, exception
  messages never embed the raw response body, and `RedactingLogger` strips
  Mybring headers from any PSR-3 logger context.
- **New endpoints.** Pickup Point, Address (replacement for legacy postal-code),
  Modify Delivery, Order Management (REST).
- **Canonical header casing.** `X-Mybring-API-Uid` / `X-Mybring-API-Key` match
  Bring's published docs exactly.
- **Bug fixes that affect v3 callers too** (these are also patched in the
  `Crakter\BringApi\*` namespaces):
  - `Base::send()` no longer calls `$e->getResponse()->getBody(true)` (removed
    in Guzzle 7).
  - XLS responses use `phpoffice/phpspreadsheet` (PHPExcel was abandoned 2017).
  - XML serialisation now escapes `& < >` properly.
  - `ApiEntityBase::toArray()` no longer marks every property `NOT_NULL`,
    so optional fields stop throwing.
  - `Authorization::get()` no longer crashes from a missing `use` import.

## Endpoint mapping (cheat sheet)

| v3 | v4 |
|----|----|
| `Clients\ShippingGuide\ShipmentPrices` | `$bring->shippingGuide()->price(PriceRequest)` |
| `Clients\ShippingGuide\ShipmentDeliveryTime` | `$bring->shippingGuide()->deliveryTime(PriceRequest)` |
| `Clients\ShippingGuide\ShipmentAll` | `$bring->shippingGuide()->products(PriceRequest)` |
| `Clients\Booking\BookShipment` | `$bring->booking()->book(BookingRequest)` |
| `Clients\Booking\OrderPickup` | `$bring->booking()->pickup(PickupRequest)` |
| `Clients\Booking\ListCustomers` | `$bring->booking()->customers()` |
| `Clients\Tracking\TrackingEndpoint` | `$bring->tracking()->track('NUMBER')` |
| `Clients\Tracking\SignatureTracking` | `$bring->tracking()->signature($id)` |
| `Clients\Reports\GenerateReport` | `$bring->reports()->generate(...)` |
| `Clients\Reports\StatusOfReport` | `$bring->reports()->status($id)` |
| `Clients\Reports\GetReport` | `$bring->reports()->download($id)` |
| `Clients\PostalCode\PostalCode` (legacy) | `$bring->postalCode()->lookup(...)` *or* `$bring->address()->postalCode(...)` |
| — | `$bring->address()->suggestions(...)` (new) |
| — | `$bring->address()->mailboxDeliveryDates(...)` (new) |
| — | `$bring->pickupPoint()->all|byId|byPostalCode|byLocation(...)` (new) |
| — | `$bring->modifyDelivery()->stopShipment|changeAddress|updateContactDetails(...)` (new) |
| — | `$bring->orderManagement()->getOrder|sendPackagingList(...)` (new REST) |

## Automated migration with Rector

For the bulk class-rename portion of the upgrade we ship a Rector ruleset.
Downstream applications can run:

```sh
composer require --dev rector/rector
vendor/bin/rector process src \
  --config vendor/crakter/bringapi/rector-v4-migration.php
```

What it does automatically:
- Renames every `Crakter\BringApi\Clients\*` endpoint class to its v4
  `Bring\Api\Endpoint\*Api` facade equivalent.
- Renames the `DefaultData\Countries|Languages|Products` constant classes
  to the v4 `Enum\Country|Language|Product` enums.
- Maps the v3 `BringClientException` to the v4 `BringApiException`.

What still needs hand-editing afterwards (Rector flags symbols, you fix
the call sites):
- Fluent `(new TrackingEntity)->setQ(...)` chains → typed request DTOs.
- `Authorization` setter chains → `new Credentials(...)`.
- `->send()->toArray()` → direct DTO accessors on the v4 return type.

## Test mode

```php
$bring = ApiClient::withCredentials($creds)->withTestMode(true);
// every request gets X-Bring-Test-Indicator: true
```

## Logging without leaking the API key

```php
use Bring\Api\ApiClient;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$logger = new Logger('bring');
$logger->pushHandler(new StreamHandler('php://stderr'));

// The factory wraps your logger in a RedactingLogger that strips the API key
// and Bring auth headers from every log line.
$bring = ApiClient::withCredentials($creds, logger: $logger);
```

## Removed / changed behaviour

- `ReturnFileTypes::XLS` responses now require `phpoffice/phpspreadsheet`
  (previously `phpoffice/phpexcel`). Both are still detected at runtime.
- `BringClientException` (v3) is unchanged but its messages no longer embed
  the raw response body. Use the wrapped previous exception's response if
  you need the body.
- `Authorization::__debugInfo()` now masks the API key — `print_r($auth)`
  outputs a fingerprint instead.
- Header values are sent with canonical casing (`X-Mybring-API-Key`, not
  `X-MyBring-API-Key`). HTTP is case-insensitive so no server-side change.

## Removed from CI

- `.travis.yml` — replaced by `.github/workflows/ci.yml` (matrix
  PHP 8.2 / 8.3 / 8.4 / 8.5 with `lowest` and `highest` deps).
- `.php_cs` — replaced by `.php-cs-fixer.dist.php`.
- The Sami documentation generator (abandoned, no PHP 8 support) is replaced
  by phpDocumentor 3. Run `composer docs` locally or push to `master`/`main` —
  the new `docs` GitHub Action builds and (optionally) publishes to Pages.
