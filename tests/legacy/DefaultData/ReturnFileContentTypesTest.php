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

use Crakter\BringApi\Exception\InputValueNotAllowedException;
use PHPUnit\Framework\TestCase;

class ReturnFileContentTypesTest extends TestCase
{
    public function testHasConstant(): void
    {
        $this->assertTrue(ReturnFileContentTypes::has('XML'), 'Test has trait on class');
        $this->assertTrue(ReturnFileContentTypes::has('text/xml'), 'Test has trait on class');
    }

    public function testHasNotConstant(): void
    {
        $this->assertFalse(ReturnFileContentTypes::has('XMML'), 'Test has trait on class');
    }

    public function testGetConstantException(): void
    {
        $this->expectException(InputValueNotAllowedException::class);
        ReturnFileContentTypes::get('XMML');
    }
}
