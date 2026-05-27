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

class CountriesTest extends TestCase
{
    public function testHasConstant(): void
    {
        $this->assertTrue(Countries::has('NORWAY'), 'Test has trait on class');
        $this->assertTrue(Countries::has('NO'), 'Test has trait on class');
    }

    public function testHasNotConstant(): void
    {
        $this->assertFalse(Countries::has('NOWAY'), 'Test has trait on class');
    }

    public function testGetConstant(): void
    {
        $this->assertEquals(Countries::get('NORWAY'), 'NO', 'Test that the constant is unchanged');
        $this->assertEquals(Countries::get('NO'), 'NO', 'Test that the constant is unchanged');
    }

    public function testGetConstantException(): void
    {
        $this->expectException(InputValueNotAllowedException::class);
        Countries::get('NOWAY');
    }
}
