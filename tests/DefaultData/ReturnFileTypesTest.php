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

use PHPUnit\Framework\TestCase;
use Crakter\BringApi\Exception\InputValueNotAllowedException;

class ReturnFileTypesTest extends TestCase
{
    public function testHasConstant()
    {
        $this->assertTrue(ReturnFileTypes::has('XML'), 'Test has trait on class');
        $this->assertTrue(ReturnFileTypes::has('xml'), 'Test has trait on class');
    }

    public function testHasNotConstant()
    {
        $this->assertFalse(ReturnFileTypes::has('XMML'), 'Test has trait on class');
    }

    public function testGetConstantException()
    {
        $this->expectException(InputValueNotAllowedException::class);
        ReturnFileTypes::get('XMML');
    }
}
