<?php

declare(strict_types=1);

/*
 * Bring API v4 — Pickup Point lookup example
 *
 * Run from project root:
 *   php examples/v4_PickupPoint.php 0150
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Bring\Api\ApiClient;
use Bring\Api\Auth\Credentials;
use Bring\Api\Enum\Country;
use Bring\Api\Exception\BringException;

$postalCode = $argv[1] ?? '0150';

$bring = ApiClient::withCredentials(new Credentials(
    uid: getenv('BRING_UID') ?: 'me@example.com',
    apiKey: getenv('BRING_API_KEY') ?: 'demo-key',
    clientUrl: 'https://example.com',
));

try {
    $points = $bring->pickupPoint()->byPostalCode(Country::NO, $postalCode);
    foreach ($points->pickupPoints as $pp) {
        printf("%-30s  %s, %s %s\n", $pp->name, $pp->address, $pp->postalCode, $pp->city);
    }
} catch (BringException $e) {
    fprintf(STDERR, "Bring error: %s\n", $e->getMessage());
    exit(1);
}
