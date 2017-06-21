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
 * BringApi PartiesEntity
 *
 * An class to supply correct information to Bring Api servers
 *
 * Quick setup: <code>$product = (new ProductEntity)
 *                     ->setId(Products::SERVICEPAKKE)
 *                     ->setCustomerNumber('PARCELS_NORWAY-10005540322');</code>
 *
 * @property bool $cashOnDelivery             Request cash on delivery
 * @property bool $recipientNotification      Notify recipient
 * @property bool $socialControl              ?
 * @property bool $simpleDelivery             Delivery without signature
 * @property bool $deliveryOption             ?
 * @property bool $saturdayDelivery           Delivery on Saturday
 * @property bool $flexDelivery               Flexible delivery
 * @property bool $phonenotification          Notify on phone
 * @property bool $deliveryIndoors            Deliver indoors
 * @method ApiEntityInterface setCashOnDelivery(bool $bool)
 * @method bool getCashOnDelivery()
 * @method ApiEntityInterface setRecipientNotification(bool $bool)
 * @method bool getRecipientNotification()
 * @method ApiEntityInterface setSocialControl(bool $bool)
 * @method bool getSocialControl()
 * @method ApiEntityInterface setDeliveryOption(bool $bool)
 * @method bool getDeliveryOption()
 * @method ApiEntityInterface setSaturdayDelivery(bool $bool)
 * @method bool getSaturdayDelivery()
 * @method ApiEntityInterface setFlexDelivery(bool $bool)
 * @method bool getFlexDelivery()
 * @method ApiEntityInterface setPhonenotification(bool $bool)
 * @method bool getPhonenotification()
 * @method ApiEntityInterface setDeliveryIndoors(bool $bool)
 * @method bool getDeliveryIndoors()
 * @author Martin Madsen <crakter@gmail.com>
 */
class ServicesEntity extends ApiEntityBase implements ApiEntityInterface
{
}
