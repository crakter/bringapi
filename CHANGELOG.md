# Changelog

All notable changes to `crakter/bringapi` are documented in this file.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Fixed
- `RetryClient` now rewinds seekable request bodies between attempts.
  Without this, retried POSTs (booking, pickup, modify-delivery) sent an
  empty body on attempt 2+ because the inner client had already read the
  stream to EOF.
- `Transport` unwraps Guzzle's `BadResponseException` (raised when a
  caller supplies a Guzzle client with the default `http_errors=true`)
  into a `BringApiException` carrying the real response, instead of
  mislabeling it as a transport failure.
- `CustomersEndpoint`, `StatusEndpoint`, `GenerateReportEndpoint`,
  `ListAvailableCustomersEndpoint`, `ListAvailableReportsForCustomerEndpoint`,
  and `ListInvoiceNumbersEndpoint` no longer accept a `$format`
  parameter that they couldn't honour: each one extends
  `AbstractJsonEndpoint`, so a non-JSON format would have triggered a
  guaranteed `JsonException` at runtime. The URL is now hardcoded to
  `.json`. `ReportsApi::download()` retains the format param — it
  returns raw bytes and is the only Reports method where the format is
  meaningful.

## [4.0.0-beta1] - 2026-05-27

First public preview of the v4 typed rewrite. **Breaking changes against
3.x** — see [UPGRADE-4.0.md](UPGRADE-4.0.md) for the full migration guide
(and the `rector-v4-migration.php` ruleset that automates most of it).

### Added
- New typed `Bring\Api\*` namespace with PSR-18/PSR-17/PSR-3 transport,
  request/response DTOs, and PHP 8.1 enums.
- New endpoint surfaces: Pickup Point API (NO/SE/DK/FI), Address API
  (per-country postal-code lookup, suggestions, mailbox-delivery dates),
  Modify Delivery API (stop/change-address/update-contact), Order
  Management API (REST).
- `Bring\Api\Auth\Credentials` — immutable value object that wraps the
  API key behind `#[\SensitiveParameter]` and masks it in `print_r` /
  `var_dump` via `__debugInfo()`.
- `Bring\Api\Logging\RedactingLogger` — PSR-3 decorator that strips
  Mybring auth headers and the literal API key from any logged context.
- `Bring\Api\Http\RetryClient` — PSR-18 decorator with exponential
  backoff + jitter, honours `Retry-After`, configurable status set.
- `Bring\Api\Http\AsyncTransport` and `ApiClient::async()` — concurrent
  fan-out via Guzzle promises, including `all()` / `settleAll()` helpers.
- `ApiClient::withTestMode()` toggle that injects `X-Bring-Test-Indicator`.
- `rector-v4-migration.php` — Rector ruleset for downstream applications.
- phpDocumentor 3 build pipeline (`composer docs`, GitHub Pages workflow).
- GitHub Actions CI matrix (PHP 8.2/8.3/8.4, lowest+highest deps),
  PHPStan level 8, Psalm errorLevel 4, Codecov coverage reporting.

### Changed
- HTTP headers normalised to Bring's documented casing:
  `X-Mybring-API-Uid`, `X-Mybring-API-Key`, `X-Bring-Client-URL`,
  `X-Bring-Test-Indicator`.
- Tracking API moved to `https://tracking.bring.com/api/v2/tracking.json`
  (the path Bring has required since May 2024).
- XLS report decoding migrated from the abandoned `phpoffice/phpexcel`
  to `phpoffice/phpspreadsheet` (PHPExcel still detected as a fallback).
- `BringApiException::getMessage()` no longer embeds the raw response
  body; the full PSR-7 response remains reachable via `getResponse()`.
- Header redaction: error messages and logger output no longer contain
  the literal API key.

### Deprecated
- The entire `Crakter\BringApi\*` namespace — classes still ship and
  still work but are marked `@deprecated` and slated for removal in 5.0.

### Fixed (v3 in-place, also reaches v3 callers)
- `Base::send()` no longer calls `$e->getResponse()->getBody(true)`
  (the boolean argument was removed in Guzzle 7).
- `Base::toJson()` XLS path no longer uses a hardcoded `/tmp` and no
  longer falls through with an undefined `$header` on the first row.
- `ApiEntityBase::toArray()` stopped marking every public property
  `NOT_NULL` on every call (used to break optional fields).
- `ApiEntityBase::checkValues()` no longer references `$checked` before
  it is initialised; validation errors now report the concrete subclass
  via `static::class`.
- `Authorization::get()` no longer crashes on the missing-value branch
  (the required `use Crakter\BringApi\Exception\ValueNotSetException`
  import was missing).
- `SimpleXMLElement::addChild()` calls now `htmlspecialchars`-escape
  values to prevent XML injection from untrusted input.
- `setReturnXml()` sets both `Accept` and `Content-type` (the other
  `setReturn*()` helpers already did).
- `AllowedServices` data key `FLEX_DELIVERY` renamed to `flexDelivery`
  to match surrounding camelCase keys and the test expectations.

### Removed
- `.travis.yml` (Travis CI free OSS is gone), `.php_cs` (php-cs-fixer
  removed `Config::create()`), Sami documentation generator (abandoned,
  no PHP 8 support).

[Unreleased]: https://github.com/crakter/bringapi/compare/v4.0.0-beta1...HEAD
[4.0.0-beta1]: https://github.com/crakter/bringapi/releases/tag/v4.0.0-beta1
