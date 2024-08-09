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
 * BringApi Languages
 *
 * Specify which languages are available by Bring Api
 *
 * Quick example: <code>Languages::NORWEGIAN</code>
 *
 * @author Martin Madsen <crakter@gmail.com>
 */
abstract class Languages
{
    use \Crakter\BringApi\Traits\Validate;

    public const NORWEGIAN = 'no';
    public const ENGLISH = 'en';
    public const DANISH = 'da';
    public const SWEDISH = 'sv';
}
