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

class ProductsTest extends TestCase
{
    public function testHasConstant(): void
    {
        $this->assertTrue(Products::has('BPAKKE_DOR_DOR'), 'Test has trait on class');
        $this->assertTrue(Products::has('BPAKKE_DOR-DOR'), 'Test has trait on class');
    }

    public function testHasNotConstant(): void
    {
        $this->assertFalse(Products::has('BPAKKE_DORDOR'), 'Test has trait on class');
    }

    public function testGetConstantException(): void
    {
        $this->expectException(InputValueNotAllowedException::class);
        Products::get('BPAKKE_DORDOR');
    }
}
