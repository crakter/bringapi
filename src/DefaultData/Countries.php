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

    public const NORWAY = 'NO';
    public const DENMARK = 'DK';
    public const SWEDEN = 'SE';
    public const FINLAND = 'FI';
    public const NETHERLAND = 'NL';
    public const GERMANY = 'DE';
    public const UNITED_STATES = 'US';
    public const BELGIUM = 'BE';
    public const FAROE_ISLANDS = 'FO';
    public const GREENLAND = 'GL';
}
