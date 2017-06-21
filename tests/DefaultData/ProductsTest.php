<?php

namespace Crakter\BringApi\DefaultData;

use PHPUnit\Framework\TestCase;
use Crakter\BringApi\Exception\InputValueNotAllowedException;

class ProductsTest extends TestCase
{
    public function testHasConstant()
    {
        $this->assertTrue(Products::has('BPAKKE_DOR_DOR'), 'Test has trait on class');
        $this->assertTrue(Products::has('BPAKKE_DOR-DOR'), 'Test has trait on class');
    }

    public function testHasNotConstant()
    {
        $this->assertFalse(Products::has('BPAKKE_DORDOR'), 'Test has trait on class');
    }

    public function testGetConstantException()
    {
        $this->expectException(InputValueNotAllowedException::class);
        Products::get('BPAKKE_DORDOR');
    }
}
