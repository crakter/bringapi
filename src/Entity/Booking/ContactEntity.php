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
 * BringApi ContactEntity
 *
 * An class to supply correct information to Bring Api servers
 *
 * Quick setup: <code>$contact = (new ContactEntity)
 *                     ->setName('Trond Nordmann');</code>
 *
 * @property string $name                   Can be set to name of contact
 * @property string $email                  Can be set to email of contact
 * @property string $phoneNumber            Can be set to phonenumber of contact
 * @method ApiEntityInterface setName(string $string)
 * @method array getName()
 * @method ApiEntityInterface setEmail(string $string)
 * @method array getEmail()
 * @method ApiEntityInterface setPhoneNumber(string $string)
 * @method array getPhoneNumber()
 * @author Martin Madsen <crakter@gmail.com>
 */
class ContactEntity extends ApiEntityBase implements ApiEntityInterface
{
}
