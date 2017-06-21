<?php

namespace Crakter\BringApi\DefaultData;

use PHPUnit\Framework\TestCase;
use Crakter\BringApi\Exception\InputValueNotAllowedException;

class HttpMethodsTest extends TestCase
{
    public function testConstantValue()
    {
        $this->assertEquals(HttpMethods::GET, 'GET', 'Test that HttpMethod::GET is equal');
        $this->assertEquals(HttpMethods::POST, 'POST', 'Test that HttpMethod::POST is equal');
    }

    public function testConstantValueWrong()
    {
        $this->assertNotEquals(HttpMethods::GET, 'POST', 'Test that HttpMethod::GET is not equal');
        $this->assertNotEquals(HttpMethods::POST, 'GET', 'Test that HttpMethod::POST is not equal');
    }
}
