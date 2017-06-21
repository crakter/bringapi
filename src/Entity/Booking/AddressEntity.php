<?php

/*
 * This file is part of the BringApi package.
 *
 * (c) Martin Madsen <crakter@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Crakter\BringApi\Entity\Booking;

use Crakter\BringApi\Entity\ApiEntityBase;
use Crakter\BringApi\Entity\ApiEntityInterface;

/**
 * BringApi AddressEntity
 *
 * An class to supply correct information to Bring Api servers
 *
 * Quick setup: <code>$address = (new AddressEntity)
 *                     ->setName('Ola Nordmann');</code>
 *
 * @property string $addressLine2           Can be set to addressline2 of address
 * @property string $reference              Can be set to a reference number/string, is searchable 1 month in bring.com
 * @property string $additionalAddressInfo  Can be set to 35 character long string for pickup/delivery instruction
 * @property array $contact                 Can be set to a array with contact info. Example: ['name' => 'Trond Nordmann']
 * @method ApiEntityInterface setName(string $string)
 * @method array getName()
 * @method ApiEntityInterface setAddressLine(string $string)
 * @method array getAddressLine()
 * @method ApiEntityInterface setAddressLine2(string $string)
 * @method array getAddressLine2()
 * @method ApiEntityInterface setPostalCode(string $string)
 * @method array getPostalCode()
 * @method ApiEntityInterface setReference(string $string)
 * @method array getReference()
 * @method ApiEntityInterface setAdditionalAddressInfo(string $string)
 * @method array getAdditionalAddressInfo()
 * @method ApiEntityInterface setContact(array $array)
 * @method array getContact()
 * @author Martin Madsen <crakter@gmail.com>
 */
class AddressEntity extends ApiEntityBase implements ApiEntityInterface
{
    /**
     * @var string $name This needs to be set to the name of address
     */
    public $name;

    /**
     * @var string $addressLine This needs to be set to the addressline of address
     */
    public $addressLine;

    /**
     * @var string $postalCode This needs to be set to the postal code of address
     */
    public $postalCode;

    /**
     * @var string $city This needs to be set to the city of address
     */
    public $city;

    /**
     * @var string $countryCode This needs to be set to the Country of address
     */
    public $countryCode;
}
