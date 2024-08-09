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
namespace Crakter\BringApi\Entity;

/**
 * BringApi PostalCodeEntity
 *
 * An class to supply correct information to Bring Api servers
 *
 * Quick setup: <code>$postalCodes = (new PostalCodeEntity)
 *                     ->setPnr('1640'); // RÅDE</code>
 *
 * @property string $clientUrl        This should be your url, does not need to be set if you are using AuthorizationInterface
 * @property string $pnr              This should be a valid postalcode, if not unknown is returned
 * @property string $country          This can be set to all countries in "Countries DefaultData"
 * @property string $callback         This can be a JSONP callback
 * @method ApiEntityInterface setClientUrl(string $string)
 * @method string getClientUrl()
 * @method ApiEntityInterface setPnr(string $string)
 * @method string getPnr()
 * @method ApiEntityInterface setCountry(string $string)
 * @method string getCountry()
 * @method ApiEntityInterface setCallback(string $string)
 * @method string getCallback()
 * @author Martin Madsen <crakter@gmail.com>
 */
class PostalCodeEntity extends ApiEntityBase implements ApiEntityInterface
{
    public string $pnr = '';
    public string $clientUrl = '';
    public string $country = '';
    public string $callback = '';

    public function setPnr(string $val): ApiEntityInterface
    {
        $this->pnr = $val;

        return $this;
    }

    public function setClientUrl(string $val): ApiEntityInterface
    {
        $this->clientUrl = $val;

        return $this;
    }

    public function setCountry(string $val): ApiEntityInterface
    {
        $this->country = $val;

        return $this;
    }

    public function setCallback(string $val): ApiEntityInterface
    {
        $this->callback = $val;

        return $this;
    }
}
