<?php

/*
 * This file is part of the BringApi package.
 *
 * (c) Martin Madsen <crakter@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Crakter\BringApi\Clients;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client;
use Crakter\BringApi\Exception\BringClientException;

class ListAvailableReportsCustomerTest extends TestCase
{
    private $class;
    private $json;

    public function setUp(): void
    {
        $this->class = (new Reports\ListAvailableReportsCustomer);
        $this->json = file_get_contents(dirname(dirname(__DIR__)).'/Data/ListAvailableReportsCustomerResponse.json');
        $this->class->setResponse(new Response(200, ['X-Foo' => 'Bar'], $this->json));
    }

    public function testGetSetCustomerId(): void
    {
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setCustomerId('x'));
        $this->assertSame('x', $this->class->getCustomerId());
    }

    public function testProcessClientUrlVariables(): void
    {
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setCustomerId('x'));
        $this->assertInstanceOf(ClientsInterface::class, $this->class->processClientUrlVariables());
    }

    public function testCheckErrorsException(): void
    {
        $this->expectException(BringClientException::class);
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setResponse(new Response(200, ['X-Foo' => 'Bar'], 'test')));
        $this->class->checkErrors();
    }

    public function testCheckErrors(): void
    {
        $this->assertInstanceOf(ClientsInterface::class, $this->class->checkErrors());
    }

    public function testSend(): void
    {
        $array = $this->class->toArray();
        $mock = new MockHandler([$this->class->getResponse()]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setClient($client));
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setCustomerId('x'));
        $this->assertInstanceOf(ClientsInterface::class, $this->class->send());
        $this->assertEquals($array, $this->class->toArray());
    }
}
