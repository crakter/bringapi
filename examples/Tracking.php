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

use Crakter\BringApi\Entity\TrackingEntity;
use Crakter\BringApi\Clients\Tracking\SignatureTracking;
use Crakter\BringApi\Clients\Tracking\TrackingEndpoint;
use Crakter\BringApi\Exception\BringClientException;
use Crakter\BringApi\Clients\Authorization;

// Gets the environment variables we have set - you can track without authorization aswell.
$apiKey = getenv('BRING_API_KEY');
$uid = getenv('BRING_UID');
$customerNumber = getenv('BRING_CUSTOMER_NUMBER');
// Your URL
$clientUrl = 'http://example.com';

// Sets the tracking query, passed through terminal like "php PostalCode.php query"
$q = $argv[1];

// Sets the authorizationModule - you can track without authorization aswell.
$credentials = (new Authorization)
    ->setApiKey($apiKey)
    ->setClientId($uid)
    ->setClientUrl($clientUrl);

// Adds the essentials for ShippingGuide
$request = (new TrackingEntity)
    ->setQ($q);

// Try to send the tracking request - or catch the error.
try {
    $result = (new TrackingEndpoint)->setAuthorizationModule($credentials)->setApiEntity($request)->send();
    // Get the reponse back in Array.
    print_r($result->toArray());
} catch (BringClientException $e) {
    print_r($e->getMessage());
}

// Start the check signature retrieval
$array = $result->toArray()['consignmentSet'];

$signatureArray = [];
$lastSignature = [];
foreach($array as $var) {
    foreach($var['packageSet'] as $v) {
        // Do not check it if it has already been checked.
        if(isset($signatureArray[$v['packageNumber']])) {
            continue;
        }
        foreach($v['eventSet'] as $val) {
            // Check if there is a signature.
            if(isset($val['recipientSignature']['linkToImage'])) {
                // Check that the signature is not the same as the last.
                if(!isset($lastSignature['signatureLink']) || $val['recipientSignature']['linkToImage'] != $lastSignature['signatureLink']) {
                    $lastSignature =  [
                        'signatureLink' => $val['recipientSignature']['linkToImage'],
                        'signature' => isset($signatureArray[$v['packageNumber']]) ? $signatureArray[$v['packageNumber']] : 0
                    ];
                    $signature = (new SignatureTracking)
                        ->setAuthorizationModule($credentials)
                        ->setReturnPng()
                        ->setSignatureLink($val['recipientSignature']['linkToImage'])
                        ->send();
                    $signatureArray[$v['packageNumber']] = base64_encode($signature->getResponse()->getBody(true));
                } else {
                    $signatureArray[$v['packageNumber']] = $lastSignature['signature'];
                }
                echo "\n";
                print_r($signatureArray[$v['packageNumber']]);
            }
        }
    }
}
