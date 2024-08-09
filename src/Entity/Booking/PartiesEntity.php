<?php

declare(strict_types=1);

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
 * BringApi PartiesEntity
 *
 * An class to supply correct information to Bring Api servers
 *
 * Quick setup: <code>$parties = (new PartiesEntity)
 *                     ->setSender(['name' => 'Ola Nordmann']);</code>
 *
 * @property string $pickupPoint        Can be set to another pickup point than recipient
 * @method ApiEntityInterface setSender(array $array)
 * @method array getSender()
 * @method ApiEntityInterface setRecipient(array $array)
 * @method array getRecipient()
 * @method ApiEntityInterface setPickupPoint(array $array)
 * @method array getPickupPoint()
 * @author Martin Madsen <crakter@gmail.com>
 */
class PartiesEntity extends ApiEntityBase implements ApiEntityInterface
{
    /**
     * @var array $sender This needs to be set to an array with information described in API
     */
    public $sender;

    /**
     * @var array $recipient This needs to be set to an array with information described in API
     */
    public $recipient;
}
