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
use Crakter\BringApi\Entity\ApiEntityBase;
use Crakter\BringApi\Entity\ApiEntityInterface;

class PostalCodeTest extends TestCase
{
    private $class;
    private $json;

    public function setUp()
    {
        $this->class = (new PostalCode\PostalCode);
        $this->json = file_get_contents(dirname(dirname(__DIR__)).'/Data/PostalCodeResponse.json');
        $this->class->setResponse(new Response(200, ['X-Foo' => 'Bar'], $this->json));
    }

    public function testProcessClientUrlVariables()
    {
        $this->assertInstanceOf(ClientsInterface::class, $this->class->processClientUrlVariables());
    }

    public function testCheckIfNormal()
    {
        $array = [
            'result' => 'OSLO',
            'valid' => true,
            'postalCodeType' => 'NORMAL',
        ];
        $this->class->setResponse(new Response(200, ['X-Foo' => 'Bar'], json_encode($array)));
        $this->assertTrue($this->class->checkIfNormal());
    }

    public function testCheckIfUnknown()
    {
        $array = [
            'result' => 'OSLO',
            'valid' => true,
            'postalCodeType' => 'UNKNOWN',
        ];
        $this->class->setResponse(new Response(200, ['X-Foo' => 'Bar'], json_encode($array)));
        $this->assertTrue($this->class->checkIfUnknown());
    }

    public function testCheckIfPoBox()
    {
        $array = [
            'result' => 'OSLO',
            'valid' => true,
            'postalCodeType' => 'POBOX',
        ];
        $this->class->setResponse(new Response(200, ['X-Foo' => 'Bar'], json_encode($array)));
        $this->assertTrue($this->class->checkIfPoBox());
    }

    public function testCheckIfSpecialCustomer()
    {
        $array = [
            'result' => 'OSLO',
            'valid' => true,
            'postalCodeType' => 'SPECIALCUSTOMER',
        ];
        $this->class->setResponse(new Response(200, ['X-Foo' => 'Bar'], json_encode($array)));
        $this->assertTrue($this->class->checkIfSpecialCustomer());
    }

    public function testCheckIfSpecialNoStreet()
    {
        $array = [
            'result' => 'OSLO',
            'valid' => true,
            'postalCodeType' => 'SPECIALNOSTREET',
        ];
        $this->class->setResponse(new Response(200, ['X-Foo' => 'Bar'], json_encode($array)));
        $this->assertTrue($this->class->checkIfSpecialNoStreet());
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
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setApiEntity(new PostalCodeEntity));
        $this->assertInstanceOf(ClientsInterface::class, $this->class->processEntity());
    }

    public function testSend()
    {
        $mock = new MockHandler([$this->class->getResponse()]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setClient($client));
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setApiEntity(new PostalCodeEntity));
        $this->assertInstanceOf(ClientsInterface::class, $this->class->send());
        $this->assertJsonStringEqualsJsonString($this->json, $this->class->toJson());
    }
}

class PostalCodeEntity extends ApiEntityBase implements ApiEntityInterface
{
}
