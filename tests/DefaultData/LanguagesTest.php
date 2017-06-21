<?php

namespace Crakter\BringApi\DefaultData;

use PHPUnit\Framework\TestCase;
use Crakter\BringApi\Exception\InputValueNotAllowedException;

class LanguagesTest extends TestCase
{
    public function testHasConstant()
    {
        $this->assertTrue(Languages::has('NORWEGIAN'), 'Test validate trait on class');
        $this->assertTrue(Languages::has('no'), 'Test validate trait on class');
    }

    public function testHasNotConstant()
    {
        $this->assertFalse(Languages::has('NORWAY'), 'Test validate trait on class');
    }

    public function testGetConstant()
    {
        $this->assertEquals(Languages::get('NORWEGIAN'), 'no', 'Test that the constant is unchanged');
    }

    public function testGetConstantException()
    {
        $this->expectException(InputValueNotAllowedException::class);
        Languages::get('NORWAY');
    }
}
