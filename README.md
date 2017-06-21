# Bring API PHP

Bring API PHP is a library to contact Bring API for prices, booking, reports and so on.
I did not find anything that i could use for my project with PHP and Bring API. So i needed a good library that was reusable.
This is used in production of a large Norwegian wholesaler.

## Install

### Composer (recommended)

Can be installed directly with [Composer](https://getcomposer.org/).

Run the following command in correct directory.
```
$ composer require crakter/bringapi
```

## Requirements

* PHP version 7.0.0 or higher
* PHP extension `php_simplexml` enabled (enabled by default in 5.1.2 and above)

## Supported Apis

- [x] Shipping Guide API
    - [x] Get estimated prices
    - [x] Get Estimated delivery time
    - [x] Get Estimated shipment prices, delivery time and more
- [x] Booking API
    - [x] List customer names
    - [x] Book Shipments
    - [x] Order pickups
- [x] Tracking API
    - [x] Track shipments (also logged in)
    - [x] Download Signature
- [x] Reports API
    - [x] List available customers
    - [x] List available reports for a customer
    - [x] Generate a report
    - [x] Check the status of a report
    - [x] List invoice numbers
- [x] Postal Code API
    - [x] Lookup postal code

## Basic usage

### Tracking as logged in

If you remove the authorizationModule you can track like normal user.

```php
use Crakter\BringApi\Entity\TrackingEntity;
use Crakter\BringApi\Clients\Authorization;
use Crakter\BringApi\Clients\Tracking\TrackingEndpoint;

$trackingEntity = (new TrackingEntity)->set(['q' => 'TESTPACKAGE-AT-PICKUPPOINT']); // Can be used like this
//$trackingEntity = (new TrackingEntity)->setQ('TESTPACKAGE-AT-PICKUPPOINT'); // Can also be used like this
/* And like this
$trackingEntity = (new TrackingEntity);
$trackingEntity->q = 'TESTPACKAGE-AT-PICKUPPOINT';
*/
$authorizationModule = (new Authorization)
    ->setApiKey('1234abc-abcd-1234-5678-abcd1234abcd')
    ->setClientId('example@example.no')
    ->setClientUrl('http://example.com');

$tracking = (new TrackingEndpoint)
    ->setAuthorizationModule($authorizationModule)
    ->setApiEntity($trackingEntity)
    ->send();
print_r($tracking->toArray());
```

## Advanced Usage

Look in the examples folder or you can generate the documents using Sami (composer dev-dependency).

For people running Linux

```
vendor/bin/sami.php -n render SamiConfig.php
```

For people running Windows

```
"vendor/bin/sami.php.bat" -n render SamiConfig.php
```

You will now find the documents in the docs/build folder.

### Running examples

For the tests to be able to run the Bring UID, Bring API Key and Customer number has to be set in the environment

For people running Linux

```
cd example/
export BRING_UID="john.doe@example.com" && export BRING_API_KEY="1234abc-abcd-1234-5678-abcd1234abcd" && export BRING_CUSTOMER_NUMBER="	PARCELS_NORWAY-10001123123"
```

For people running Windows

```
cd examples/
setx BRING_UID me@myemail.com
setx BRING_API_KEY xxxxxx-xxxxx-xxx-xxxx
setx BRING_CUSTOMER_NUMBER BRING__CUSTOMER_NUMBER
```

Go into a new command prompt and run the examples.
Some examples can be passed with arguments like postalcodes.
```
php BookAndPickupShipment.php
php PostalCode.php <numberOfPostalCode>
php ShippingGuideAll.php <fromPostalCode> <toPostalCode>
php Tracking.php <query>
php Reports.php <reportId>
```

## Contribute

Contributions are very welcomed.

Please follow PSR-2 coding standard. You can run php-cs-fixer to fix the problems in the code.

## License

MIT
