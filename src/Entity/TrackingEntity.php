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
 * BringApi TrackingEntity
 *
 * An class to supply correct information to Bring Api servers
 *
 * Quick setup: <code>$reports = (new TrackingEntity)
 *                     ->setQ('7071737171');</code>
 *
 * @property string $q                This should be either trackingnumber or Reference number
 * @property string $callback         This can be a JSONP callback
 * @method ApiEntityInterface setCallback(string $string)
 * @method string getCallback()
 * @method ApiEntityInterface setQ(string $string)
 * @method string getQ()
 * @author Martin Madsen <crakter@gmail.com>
 */
class TrackingEntity extends ApiEntityBase implements ApiEntityInterface
{
    public $q = '';

    public function setQ(string $val): ApiEntityInterface
    {
        $this->q = $val;

        return $this;
    }

    public function setCallback(string $val): ApiEntityInterface
    {
        $this->callback = $val;

        return $this;
    }
}
