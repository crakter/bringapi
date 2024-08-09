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
 * BringApi ReturnFileTypes
 *
 * Specify which file types are available by Bring Api
 *
 * Quick example: <code>ReturnFileTypes::JSON</code>
 *
 * @author Martin Madsen <crakter@gmail.com>
 */
abstract class ReturnFileTypes
{
    use \Crakter\BringApi\Traits\Validate;

    /**
     * Accepted return filetypes from Bring API.
     * All client apis are not supportive of all return filetypes.
     */
    public const XML = 'xml';
    public const JSON = 'json';
    public const PDF = 'pdf';
    public const XLS = 'xls';
    public const HTML = 'html';
    public const PNG = 'png';
}
