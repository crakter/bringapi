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
 * Quick setup: <code>$dimensions = (new DimensionsEntity)
 *                     ->setHeightInCm(5)
 *                     ->setWidthInCm(5)
 *                     ->setLengthInCm(5);</code>
 *
 * @property int $heightInCm                Height dimension of package in cm
 * @property int $widthInCm                 Width dimension of package in cm
 * @property int $lengthInCm                Length dimension of package in cm
 * @method ApiEntityInterface setHeightInCm(int $int)
 * @method int getHeightInCm()
 * @method ApiEntityInterface setWidthInCm(int $int)
 * @method int getWidthInCm()
 * @method ApiEntityInterface setLengthInCm(int $int)
 * @method int getLengthInCm()
 * @author Martin Madsen <crakter@gmail.com>
 */
class DimensionsEntity extends ApiEntityBase implements ApiEntityInterface
{
}
