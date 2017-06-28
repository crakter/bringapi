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
use Crakter\BringApi\Exception\ApiEntityNotCorrectException;
use Crakter\BringApi\Entity\ReportsEntity;

class GenerateReportTest extends TestCase
{
    private $class;
    private $json;

    public function setUp()
    {
        $this->class = (new Reports\GenerateReport);
        $this->json = file_get_contents(dirname(dirname(__DIR__)).'/Data/GenerateReportResponse.json');
        $this->class->setResponse(new Response(200, ['X-Foo' => 'Bar'], $this->json));
    }

    public function testGetStatusUrl()
    {
        $this->assertSame('https://www.mybring.com/reports/api/report/db285042-6e8d-4563-94ca-eb1100706a73/status/', $this->class->getStatusUrl());
    }

    public function testGetReportId()
    {
        $this->assertSame('db285042-6e8d-4563-94ca-eb1100706a73', $this->class->getReportId());
    }

    public function testGetSetCustomerId()
    {
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setCustomerId('x'));
        $this->assertSame('x', $this->class->getCustomerId());
    }

    public function testGetSetReportTypeId()
    {
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setReportTypeId('x'));
        $this->assertSame('x', $this->class->getReportTypeId());
    }

    public function testProcessClientUrlVariables()
    {
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setCustomerId('x'));
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setReportTypeId('x'));
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
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setResponse(new Response(200, ['X-Foo' => 'Bar'], '{"q":"testing"}')));
        $this->assertInstanceOf(ClientsInterface::class, $this->class->checkErrors());
    }

    public function testProcessEntityException()
    {
        $this->expectException(ApiEntityNotCorrectException::class);
        $this->assertInstanceOf(ClientsInterface::class, $this->class->processEntity());
    }

    public function testProcessEntity()
    {
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setApiEntity(new ReportsEntity()));
        $this->assertInstanceOf(ClientsInterface::class, $this->class->processEntity());
    }

    public function testSend()
    {
        $mock = new MockHandler([$this->class->getResponse()]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setClient($client));
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setApiEntity(new ReportsEntity()));
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setCustomerId('x'));
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setReportTypeId('x'));
        $this->assertInstanceOf(ClientsInterface::class, $this->class->send());
        $this->assertJsonStringEqualsJsonString($this->json, $this->class->toJson());
    }
}
