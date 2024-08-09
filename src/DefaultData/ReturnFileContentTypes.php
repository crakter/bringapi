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
 * BringApi ReturnFileContentTypes
 *
 * Specify which file content types are available by Bring Api
 *
 * Quick example: <code>ReturnFileContentTypes::JSON</code>
 *
 * @author Martin Madsen <crakter@gmail.com>
 */
abstract class ReturnFileContentTypes
{
    use \Crakter\BringApi\Traits\Validate;

    /**
     * Accepted return filecontenttypes from Bring API.
     * All methods are not supportive of all return filetypes.
     */
    public const XML = 'text/xml';
    public const JSON = 'application/json';
    public const HTML = 'text/html';
    public const PNG = 'image/png';
    public const XLS = 'application/vnd.ms-excel';
}
