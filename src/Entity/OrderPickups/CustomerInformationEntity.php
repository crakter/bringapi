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
 * BringApi CustomerInformationEntity
 *
 * An class to supply correct information to Bring Api servers
 *
 * Quick setup: <code>$customerInformation = (new CustomerInformationEntity)
 *                     ->setCustomerNumber('PARCELS_NORWAY-12340056789');</code>
 *
 * @method ApiEntityInterface setCustomerNumber(string $string)
 * @method string getCustomerNumber()
 * @method ApiEntityInterface setCompanyName(string $string)
 * @method string getCompanyName()
 * @author Martin Madsen <crakter@gmail.com>
 */
class CustomerInformationEntity extends ApiEntityBase implements ApiEntityInterface
{
    /**
     * @var string $customerNumber This needs to be set to the customerNumber. <code>'PARCELS_NORWAY-12340056789'</code>
     */
    public $customerNumber;

    /**
     * @var string $companyName This needs to be set to the name of the company. <code>'Norsk Bedrift AS'</code>
     */
    public $companyName;
}
