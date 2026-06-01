<?php

declare(strict_types=1);

/*
 * This file is part of the BringApi package.
 *
 * (c) Martin Madsen <crakter@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Crakter\BringApi\Clients;

use Crakter\BringApi\DefaultData\ReturnFileTypes;
use Crakter\BringApi\Exception\BringClientException;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class GetReportTest extends TestCase
{
    private $class;
    private $xml;

    public function setUp(): void
    {
        $this->class = (new Reports\GetReport());
        $this->xml = file_get_contents(dirname(dirname(__DIR__)).'/Data/GetReportResponse.xml');
        $this->class->setResponse(new Response(200, ['X-Foo' => 'Bar'], $this->xml));
    }

    public function testGetSetReportId(): void
    {
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setReportId('x'));
        $this->assertSame('x', $this->class->getReportId());
    }

    public function testGetEndPoint(): void
    {
        $this->assertSame(ReturnFileTypes::XML, $this->class->getEndPoint());
    }

    public function testProcessClientUrlVariables(): void
    {
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setReportId('x'));
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
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setReportId('x'));
        $this->assertInstanceOf(ClientsInterface::class, $this->class->send());
        $this->assertEquals($array, $this->class->toArray());
    }
}
