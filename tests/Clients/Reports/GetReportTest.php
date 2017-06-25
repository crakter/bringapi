<?php

namespace Crakter\BringApi\Clients;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client;
use Crakter\BringApi\Exception\BringClientException;
use Crakter\BringApi\Exception\ApiEntityNotCorrectException;
use Crakter\BringApi\DefaultData\ReturnFileTypes;

class GetReportTest extends TestCase
{
    private $class;
    private $xml;

    public function setUp()
    {
        $this->class = (new Reports\GetReport);
        $this->xml = file_get_contents(dirname(dirname(__DIR__)).'/Data/GetReportResponse.xml');
        $this->class->setResponse(new Response(200, ['X-Foo' => 'Bar'], $this->xml));
    }

    public function testGetSetReportId()
    {
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setReportId('x'));
        $this->assertSame('x', $this->class->getReportId());
    }

    public function testGetEndPoint()
    {
        $this->assertSame(ReturnFileTypes::XML, $this->class->getEndPoint());
    }

    public function testProcessClientUrlVariables()
    {
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setReportId('x'));
        $this->assertInstanceOf(ClientsInterface::class, $this->class->processClientUrlVariables());
    }

    public function testCheckErrorsException()
    {
        $this->expectException(BringClientException::class);
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setResponse(new Response(200, ['X-Foo' => 'Bar'], 'test')));
        $this->class->checkErrors();
    }

    public function testCheckErrors()
    {
        $this->assertInstanceOf(ClientsInterface::class, $this->class->checkErrors());
    }

    public function testSend()
    {
        $array = $this->class->toArray();
        $mock = new MockHandler([$this->class->getResponse()]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setClient($client));
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setReportId('x'));
        $this->assertInstanceOf(ClientsInterface::class, $this->class->send());
        $this->assertEquals($array, $this->class->toArray());
    }
}
