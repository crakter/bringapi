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

use Crakter\BringApi\Entity\ShippingGuideEntity;
use Crakter\BringApi\Clients\ShippingGuide\ShipmentAll;
use Crakter\BringApi\DefaultData\Countries;
use Crakter\BringApi\DefaultData\Products;
use Crakter\BringApi\DefaultData\AdditionalProducts;
use Crakter\BringApi\Exception\BringClientException;
use Crakter\BringApi\Clients\Authorization;

// Gets the environment variables we have set
$apiKey = getenv('BRING_API_KEY');
$uid = getenv('BRING_UID');
$customerNumber = getenv('BRING_CUSTOMER_NUMBER');
// Your URL
$clientUrl = 'http://example.com';

// Sets the postal code from - to, passed through terminal like "php PostalCode.php 0278 1712"
$fromPostalCode = $argv[1];
$toPostalCode = $argv[2];

// Sets the authorizationModule
$credentials = (new Authorization)
    ->setApiKey($apiKey)
    ->setClientId($uid)
    ->setClientUrl($clientUrl);

// Send package in 5 hours..
$shipDate = new \DateTime('now');
$shipDate->modify('+5 hours');

// Adds the essentials for ShippingGuide
$request = (new ShippingGuideEntity)
    ->setClientUrl($clientUrl)
    ->setFrom($fromPostalCode)
    ->setTo($toPostalCode)
    ->setCustomerNumber($customerNumber)
    ->setDate($shipDate)
    ->setTime($shipDate)
    ->setFromCountry(Countries::NORWAY)
    ->setToCountry(Countries::NORWAY)
    ->setEdi(true)
    ->setPostingAtPostOffice(false);

// Add the products you want the information about.
$products = [
    Products::BPAKKE_DOR_DOR,
    Products::BPAKKE_DOR_DOR,
    Products::SERVICEPAKKE,
    Products::EKSPRESS09,
];
foreach ($products as $v) {
    $request->addProduct($v);
}

// Add additional services to products.
$additional = [
    AdditionalProducts::ENOTIFICATION,
    AdditionalProducts::COD,
];
foreach ($additional as $v) {
    $request->addAdditional($v);
}

// Set how many packages in each category. So there is 2 packages with 10 kg and 1 package with 5 kg.
$packages = [
    2,
    1,
];
// Sets the weight.
$weightInKg = [
    10,
    5,
];
foreach ($weightInKg as $k => $v) {
    $weight = $v * 1000;
    for($x = 0; $x < $packages[$k]; $x++) {
        $request->addWeightInGrams($weight);
    }
}

// Sets the width of packages (Optional)
$width = [
    40,
    40,
];
foreach ($width as $k => $v) {
    for($x = 0; $x < $packages[$k]; $x++) {
        $request->addWidth($v);
    }
}

// Sets the height of packages (Optional)
$height = [
    60,
    30,
];
foreach ($height as $k => $v) {
    for($x = 0; $x < $packages[$k]; $x++) {
        $request->addHeight($v);
    }
}

// Sets the length of packages (Optional)
$length = [
    60,
    60,
];
foreach ($length as $k => $v) {
    for($x = 0; $x < $packages[$k]; $x++) {
        $request->addLength($v);
    }
}
try {
    $result = (new ShipmentAll)->setAuthorizationModule($credentials)->setApiEntity($request)->send();
    print_r($result->toArray());
} catch (BringClientException $e) {
    print_r($e->getMessage());
}
