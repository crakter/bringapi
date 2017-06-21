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
 * BringApi ProductEntity
 *
 * An class to supply correct information to Bring Api servers
 *
 * Quick setup: <code>$packages = (new PackagesEntity)
 *                     ->setWeightInKg(1.1);</code>
 *
 * @property string $goodsDescription                           Optional. Describe what is inside the package. This parameter can be maximum 35 character long.
 * @property string $containerId                                Optional. Specify the container identification in which dangerous good is packed. This parameter can be maximum 35 character long. Example: 1236
 * @property string $numberOfItems                              Optional. Specify the number of packages.
 * @property string $packageType                                ?
 * @property string $correlationId                              Optional. Specify something to correlate packages which belong to same order
 * @method ApiEntityInterface setWeightInKg(double $double)
 * @method float getWeightInKg()
 * @method ApiEntityInterface setContainerId(string $string)
 * @method string getContainerId()
 * @method ApiEntityInterface setPackageType(string $string)
 * @method string getPackageType()
 * @method ApiEntityInterface setCorrelationId(string $string)
 * @method string getCorrelationId()
 * @author Martin Madsen <crakter@gmail.com>
 */
class PackagesEntity extends ApiEntityBase implements ApiEntityInterface
{
    /**
     * @var float $weightInKg This needs to be set to the weight in Kg. <code>1.1</code>
     */
    public $weightInKg;

    /**
     * @var array $dimensions Dimensions of the package sent. <code>['heightInCm' => 13, 'widthInCm' => 23, 'lengthInCm' => 10]</code>
     */
    public $dimensions = [
        'heightInCm' => 1,
        'widthInCm' => 1,
        'lengthInCm' => 1,
    ];
}
