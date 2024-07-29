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

use Crakter\BringApi\Entity\ApiEntityBase;
use Crakter\BringApi\Entity\ApiEntityInterface;

/**
 * BringApi ParcelsInformationEntity
 *
 * An class to supply correct information to Bring Api servers
 *
 * Quick setup: <code>$parcelsInformation = (new ParcelsInformationEntityEntity)
 *                     ->setNumberOfPackages(2);</code>
 *
 * @method ApiEntityInterface setNumberOfPackages(int $int)
 * @method int getNumberOfPackages()
 * @method ApiEntityInterface setNumberOfPostContainers(int $int)
 * @method int getNumberOfPostContainers()
 * @method ApiEntityInterface setNumberOfPallets(int $int)
 * @method int getNumberOfPallets()
 * @author Martin Madsen <crakter@gmail.com>
 */
class ParcelsInformationEntity extends ApiEntityBase implements ApiEntityInterface
{
    /**
     * @var int $numberOfPackages      Number of packages to pickup. <code>2</code>
     */
    public $numberOfPackages = 0;

    /**
     * @var int $numberOfPostContainers      Number of post containers to pickup. <code>2</code>
     */
    public $numberOfPostContainers = 0;

    /**
     * @var int $numberOfPallets      Number of pallets to pickup. <code>2</code>
     */
    public $numberOfPallets = 0;

    public function setNumberOfPackages(int $val): ApiEntityInterface
    {
        $this->numberOfPackages = $val;

        return $this;
    }

    public function setNumberOfPostContainers(int $val): ApiEntityInterface
    {
        $this->numberOfPostContainers = $val;

        return $this;
    }

    public function setNumberOfPallets(int $val): ApiEntityInterface
    {
        $this->numberOfPallets = $val;

        return $this;
    }
}
