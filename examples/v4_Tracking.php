<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Bring\Api\ApiClient;
use Bring\Api\Auth\Credentials;
use Bring\Api\Exception\BringException;

$query = $argv[1] ?? 'TESTPACKAGE-AT-PICKUPPOINT';

$bring = ApiClient::withCredentials(new Credentials(
    uid: getenv('BRING_UID') ?: 'me@example.com',
    apiKey: getenv('BRING_API_KEY') ?: 'demo-key',
    clientUrl: 'https://example.com',
));

try {
    $resp = $bring->tracking()->track($query);
    foreach ($resp->consignments as $c) {
        printf("consignment %s\n", $c->consignmentId);
        foreach ($c->packages as $pkg) {
            printf("  package %s — %s\n", $pkg->packageNumber, $pkg->statusDescription ?? '-');
            foreach ($pkg->events as $event) {
                printf("    %s  %s (%s)\n",
                    $event->dateIso?->format(DATE_ATOM) ?? '-',
                    $event->description,
                    $event->city ?? '-',
                );
            }
        }
    }
} catch (BringException $e) {
    fprintf(STDERR, "Bring error: %s\n", $e->getMessage());
    exit(1);
}
