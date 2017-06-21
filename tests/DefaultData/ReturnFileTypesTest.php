<?php

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
