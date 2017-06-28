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
