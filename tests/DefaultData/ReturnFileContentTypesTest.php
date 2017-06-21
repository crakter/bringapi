<?php

namespace Crakter\BringApi\DefaultData;

use PHPUnit\Framework\TestCase;
use Crakter\BringApi\Exception\InputValueNotAllowedException;

class ReturnFileContentTypesTest extends TestCase
{
    public function testHasConstant()
    {
        $this->assertTrue(ReturnFileContentTypes::has('XML'), 'Test has trait on class');
        $this->assertTrue(ReturnFileContentTypes::has('text/xml'), 'Test has trait on class');
    }

    public function testHasNotConstant()
    {
        $this->assertFalse(ReturnFileContentTypes::has('XMML'), 'Test has trait on class');
    }

    public function testGetConstantException()
    {
        $this->expectException(InputValueNotAllowedException::class);
        ReturnFileContentTypes::get('XMML');
    }
}
