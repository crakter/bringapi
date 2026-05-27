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

class LanguagesTest extends TestCase
{
    public function testHasConstant(): void
    {
        $this->assertTrue(Languages::has('NORWEGIAN'), 'Test validate trait on class');
        $this->assertTrue(Languages::has('no'), 'Test validate trait on class');
    }

    public function testHasNotConstant(): void
    {
        $this->assertFalse(Languages::has('NORWAY'), 'Test validate trait on class');
    }

    public function testGetConstant(): void
    {
        $this->assertEquals(Languages::get('NORWEGIAN'), 'no', 'Test that the constant is unchanged');
    }

    public function testGetConstantException(): void
    {
        $this->expectException(InputValueNotAllowedException::class);
        Languages::get('NORWAY');
    }
}
