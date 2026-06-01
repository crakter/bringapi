<?php

declare(strict_types=1);

/*
 * Bring API v4 — Postal-code lookup example
 *
 * Run from project root:
 *   php examples/v4_PostalCode.php 0150
 *   php examples/v4_PostalCode.php 0150 NO
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Bring\Api\ApiClient;
use Bring\Api\Auth\Credentials;
use Bring\Api\Enum\Country;
use Bring\Api\Exception\BringException;

$postalCode = $argv[1] ?? '0150';
$country = Country::tryFrom($argv[2] ?? 'NO') ?? Country::NO;

$bring = ApiClient::withCredentials(new Credentials(
    uid: getenv('BRING_UID') ?: 'me@example.com',
    apiKey: getenv('BRING_API_KEY') ?: 'demo-key',
    clientUrl: 'https://example.com',
));

try {
    // Modern endpoint (preferred):
    $modern = $bring->address()->postalCode($country, $postalCode);
    printf("address API → city=%s, type=%s\n", $modern->city, $modern->postalCodeType ?? '-');

    // Legacy endpoint (still works, marked @deprecated):
    $legacy = $bring->postalCode()->lookup($postalCode, $country);
    printf("legacy API  → city=%s, valid=%s\n", $legacy->city ?? '?', $legacy->valid ? 'yes' : 'no');
} catch (BringException $e) {
    fprintf(STDERR, "Bring error: %s\n", $e->getMessage());
    exit(1);
}
