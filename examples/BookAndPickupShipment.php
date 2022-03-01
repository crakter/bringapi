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

use Crakter\BringApi\Entity\Booking\AddressEntity;
use Crakter\BringApi\Entity\Booking\ConsignmentsEntity;
use Crakter\BringApi\Entity\Booking\ContactEntity;
use Crakter\BringApi\Entity\Booking\PartiesEntity;
use Crakter\BringApi\Entity\BookShipmentsEntity;
use Crakter\BringApi\Entity\Booking\ProductEntity;
use Crakter\BringApi\Entity\Booking\PackagesEntity;
use Crakter\BringApi\Entity\OrderPickups\CustomerInformationEntity;
use Crakter\BringApi\Entity\OrderPickups\ParcelsInformationEntity;
use Crakter\BringApi\Entity\OrderPickups\PickupAddressEntity;
use Crakter\BringApi\Entity\OrderPickupsEntity;
use Crakter\BringApi\Clients\Booking\OrderPickup;
use Crakter\BringApi\Clients\Booking\BookShipment;
use Crakter\BringApi\Clients\Authorization;
use Crakter\BringApi\DefaultData\Products;
use Crakter\BringApi\DefaultData\Countries;

// Gets the environment variables we have set
$apiKey = getenv('BRING_API_KEY');
$uid = getenv('BRING_UID');
$customerNumber = getenv('BRING_CUSTOMER_NUMBER');
// Your URL
$clientUrl = 'http://example.com';

// Sets the testmode to false if in production
$testMode = true;

//Sets the contact for the Sender address
$contact = (new ContactEntity)->setName('Trond Nordmann')->setEmail('trond@nordmanntest.no')->setPhoneNumber('99999999');
//Sets the Sender adress
$sender = (new AddressEntity)
    ->setName('Ola Nordmann')
    ->setAddressLine('Testsvingen 12')
    ->setPostalCode('0263')
    ->setCity('OSLO')
    ->setCountryCode(Countries::NORWAY)
    ->setContact($contact->toArray());

//Sets the contact for the Recipient address
$contact = (new ContactEntity)->setName('Kari Nordmann')->setEmail('kari@nordmanntest.no')->setPhoneNumber('99999999');
//Sets the Recipient adress
$recipient = (new AddressEntity)
    ->setName('Kari Nordmann')
    ->setAddressLine('Testsvingen 12')
    ->setPostalCode('7030')
    ->setCity('TRONDHEIM')
    ->setCountryCode(Countries::NORWAY)
    ->setContact($contact->toArray());

// Sets the parties for address
$parties = (new PartiesEntity)->setSender($sender->toArray())->setRecipient($recipient->toArray());

// Sets the shipment product
$product = (new ProductEntity)->setId(Products::BPAKKE_DOR_DOR)->setCustomerNumber($customerNumber);

$consignment = (new ConsignmentsEntity)
    ->setParties($parties->toArray())
    ->setProduct($product->toArray())
    ->setShippingDateTime((new \DateTime)->modify('+5 hours'));
// Sets the weight for packages - 3 packages
for ($x = 0; $x < 2; $x++) {
    $consignmentPackage = (new PackagesEntity)
        ->setWeightInKg(10);
    $consignment->addPackage($consignmentPackage->toArray());
}

// Add the one consignment we have here.
$bookShipments = (new BookShipmentsEntity)->addConsignment($consignment->toArray());

// Sets the authorizationModule to be able to book and pickup.
$authorizationModule = (new Authorization)
    ->setApiKey($apiKey)
    ->setClientId($uid)
    ->setClientUrl($clientUrl);
// Try to send the booking - or catch the error.
try {
    $bookShipment = (new BookShipment)->setAuthorizationModule($authorizationModule)->setApiEntity($bookShipments)->send();
    print_r($bookShipment->toArray());
} catch (\Exception $e) {
    print_r($e->getMessage());
}

// Convert sender adress to pickup address.
$sender = (new PickupAddressEntity)->convertBookingAddress($sender);
// Sets the packagecount
$parcels = (new ParcelsInformationEntity)
    ->setNumberOfPackages(3);
// Sets the customer information
$customerInformation = (new CustomerInformationEntity)
    ->setCustomerNumber($customerNumber)
    ->setCompanyName('Ola Nordmann');
// Sets the information about the pickup.
$packages = (new OrderPickupsEntity)
    ->setCustomerInformation($customerInformation->toArray())
    ->setPickupAddress($sender->toArray())
    ->setParcelsInformation($parcels->toArray())
    ->setPickupDate((new \DateTime)->modify('+5 hours'))
    ->setPickupIsReadyAtTime((new \DateTime)->modify('+5 hours'))
    ->setCountryCode(Countries::NORWAY)
    ->setTestIndicator($testMode);

// Try to send the pickup - or catch the error.
try {
    $pickupRequest = (new OrderPickup)->setAuthorizationModule($authorizationModule)->setApiEntity($packages)->send();
    print_r($pickupRequest->toArray());
} catch (BringClientException $e) {
    print_r($e->getMessage());
}
