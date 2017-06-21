<?php

/*
 * This file is part of the BringApi package.
 *
 * (c) Martin Madsen <crakter@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Crakter\BringApi\Entity;

/**
 * BringApi BookShipmentsEntity
 *
 * An class to supply correct information to Bring Api servers
 *
 * Quick setup: <code>$bookShipments = (new BookShipmentsEntity)
 *                     ->setTestIndicator(false);</code>
 *
 * @method ApiEntityInterface setTestIndicator(bool $bool)
 * @method bool getTestIndicator()
 * @method ApiEntityInterface setSchemaVersion(int $int)
 * @method string getSchemaVersion()
 * @method ApiEntityInterface setConsignments(array $array)
 * @method string getConsignments()
 * @author Martin Madsen <crakter@gmail.com>
 */
class BookShipmentsEntity extends ApiEntityBase implements ApiEntityInterface
{
    /**
     * @var bool $testIndicator This needs to be set to true if testing or false if production
     */
    public $testIndicator = true;

    /**
     * @var int $schemaVersion This needs to be set to version of API, defaults to 1
     */
    public $schemaVersion = 1;

    /**
     * @var array $consignments This needs to be set to an array of consignments
     */
    public $consignments = [];

    /**
     * Add consignment array to consignments
     * @param  array              $consignment Example: ConsignmentsEntity->toArray()
     * @return ApiEntityInterface
     */
    public function addConsignment(array $consignment): ApiEntityInterface
    {
        $this->consignments[] = $consignment;

        return $this;
    }
}
