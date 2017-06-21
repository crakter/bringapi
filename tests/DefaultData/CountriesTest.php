<?php

namespace Crakter\BringApi\DefaultData;

use PHPUnit\Framework\TestCase;
use Crakter\BringApi\Exception\InputValueNotAllowedException;

class CountriesTest extends TestCase
{
    public function testHasConstant()
    {
        $this->assertTrue(Countries::has('NORWAY'), 'Test has trait on class');
        $this->assertTrue(Countries::has('NO'), 'Test has trait on class');
    }

    public function testHasNotConstant()
    {
        $this->assertFalse(Countries::has('NOWAY'), 'Test has trait on class');
    }

    public function testGetConstant()
    {
        $this->assertEquals(Countries::get('NORWAY'), 'NO', 'Test that the constant is unchanged');
        $this->assertEquals(Countries::get('NO'), 'NO', 'Test that the constant is unchanged');
    }

    public function testGetConstantException()
    {
        $this->expectException(InputValueNotAllowedException::class);
        Countries::get('NOWAY');
    }
}
