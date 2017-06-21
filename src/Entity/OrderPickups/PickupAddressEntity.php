<?php

/*
 * This file is part of the BringApi package.
 *
 * (c) Martin Madsen <crakter@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Crakter\BringApi\Entity\OrderPickups;

use Crakter\BringApi\Entity\Booking\AddressEntity;
use Crakter\BringApi\Entity\ApiEntityBase;
use Crakter\BringApi\Entity\ApiEntityInterface;

/**
 * BringApi PickupAddressEntity
 *
 * An class to supply correct information to Bring Api servers
 *
 * Quick setup: <code>$pickupAddress = (new PickupAddressEntity)
 *                     ->setStreet('Testsvingen 12');</code>
 *
 * @property string $email                                      This can be set to the email of person in charge of pickup. <code>'norsk.bedrift@example.com'</code>
 * @property string $phoneNumber                                This can be set to the phone number of pickup. <code>'99999999'</code>
 * @property string $message                                    This can be set to a message to the driver
 * @method ApiEntityInterface setStreet(string $string)
 * @method string getStreet()
 * @method ApiEntityInterface setPostalCode(string $string)
 * @method string getPostalCode()
 * @method ApiEntityInterface setCity(string $string)
 * @method string getCity()
 * @method ApiEntityInterface setEmail(string $string)
 * @method string getEmail()
 * @method ApiEntityInterface setPhoneNumber(string $string)
 * @method string getPhoneNumber()
 * @method ApiEntityInterface setMessage(string $string)
 * @method string getMessage()
 * @author Martin Madsen <crakter@gmail.com>
 */
class PickupAddressEntity extends ApiEntityBase implements ApiEntityInterface
{
    /**
     * @var string $street      This needs to be set to the address of pickup. <code>'Testsvingen 12'</code>
     */
    public $street;

    /**
     * @var string $postalCode  This needs to be set to the Postal code of pickup. <code>'0263'</code>
     */
    public $postalCode;

    /**
     * @var string $city        This needs to be set to the City of pickup. <code>'Oslo'</code>
     */
    public $city;

    /**
     * Convert Booking AddressEntity to this PickupAddressEntity so we don't need to pass the same info twice.
     * @param  AddressEntity      $entity Populated AddressEntity object
     * @return ApiEntityInterface
     */
    public function convertBookingAddress(AddressEntity $entity): ApiEntityInterface
    {
        $this->street = $entity->addressLine;
        $this->postalCode = $entity->postalCode;
        $this->city = $entity->city;
        if (isset($entity->contact['email'])) {
            $this->email = $entity->contact['email'];
        }
        if (isset($entity->contact['phoneNumber'])) {
            $this->phoneNumber = $entity->contact['phoneNumber'];
        }

        return $this;
    }
}
