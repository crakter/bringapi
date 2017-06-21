<?php

/*
 * This file is part of the BringApi package.
 *
 * (c) Martin Madsen <crakter@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Crakter\BringApi\DefaultData;

/**
 * BringApi Countries
 *
 * Specify which Countries are available by Bring Api
 *
 * Quick example: <code>Countries::NORWAY</code>
 * Quick example if valid: <code>Countries::has('NORWAY');</code>
 *
 * @author Martin Madsen <crakter@gmail.com>
 */
abstract class Countries
{
    use \Crakter\BringApi\Traits\Validate;

    const NORWAY = 'NO';
    const DENMARK = 'DK';
    const SWEDEN = 'SE';
    const FINLAND = 'FI';
    const NETHERLAND = 'NL';
    const GERMANY = 'DE';
    const UNITED_STATES = 'US';
    const BELGIUM = 'BE';
    const FAROE_ISLANDS = 'FO';
    const GREENLAND = 'GL';
}
