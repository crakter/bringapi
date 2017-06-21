<?php

/*
 * This file is part of the BringApi package.
 *
 * (c) Martin Madsen <crakter@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once dirname(__DIR__).'/vendor/autoload.php';

use Crakter\BringApi\Entity\PostalCodeEntity;
use Crakter\BringApi\Clients\PostalCode\PostalCode;
use Crakter\BringApi\DefaultData\Countries;
use Crakter\BringApi\Exception\BringClientException;

// Your URL
$clientUrl = 'http://example.com';
// Postal code, passed through terminal like "php PostalCode.php 0278"
$postalCode = $argv[1];
// Country name, passed through terminal like "php PostalCode.php 0278 NORWAY/SWEDEN/DENMARK/FINLAND/NETHERLAND/GERMANY/UNITED_STATES/BELGIUM/FAROE_ISLANDS/GREENLAND"
if(isset($argv[2])) {
    // Check that the country exist in the constant class.
    $country = Countries::has($argv[2]) ? Countries::get($argv[2]) : Countries::NORWAY;
} else {
    $country = Countries::NORWAY;
}

$request = (new PostalCodeEntity)
    ->setClientUrl($clientUrl)
    ->setPnr($postalCode)
    ->setCountry($country);

// Try to send the postalcode request - or catch the error.
try {
    $result = (new PostalCode)
        ->setApiEntity($request)
        ->send();
    // Get the reponse back in Array.
    print_r($result->toArray());
} catch (BringClientException $e) {
    print_r($e->getMessage());
}
