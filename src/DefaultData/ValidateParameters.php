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
 * BringApi ValidateParameters
 *
 * Specify which validation parameters are available to use
 *
 * Quick example: <code>ValidateParameters::NOT_NULL</code>
 *
 * @author Martin Madsen <crakter@gmail.com>
 */
abstract class ValidateParameters
{
    /**
     * Accepted validation parameters
     */
    public const NOT_NULL = 'notnull';
}
