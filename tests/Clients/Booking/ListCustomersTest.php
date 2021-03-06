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

class ListCustomersTest extends TestCase
{
    private $class;
    private $json;

    public function setUp(): void
    {
        $this->class = (new Booking\ListCustomers);
        $this->json = file_get_contents(dirname(dirname(__DIR__)).'/Data/ListCustomerNumbersResponse.json');
        $this->class->setResponse(new Response(200, ['X-Foo' => 'Bar'], $this->json));
    }

    public function testGetProductsCustomer(): void
    {
        $this->assertSame($this->class->getProductsCustomer('PARCELS_NORWAY-00012341234'), [
          "SERVICEPAKKE",
          "EKSPRESS09",
          "BEDRIFTSPAKKE",
          "BPAKKE_DOR-DOR",
          "PA_DOREN",
          "BPAKKE_DOR-DOR_RETURSERVICE",
          "EKSPRESS09_RETURSERVICE",
          "SERVICEPAKKE_RETURSERVICE",
          "MINIPAKKE",
          "BUSINESS_PARCEL",
          "PICKUP_PARCEL",
          "BUSINESS_PALLET",
          "BUSINESS_PARCEL_HALFPALLET",
          "BUSINESS_PARCEL_QUARTERPALLET",
          "BUSINESS_PARCEL_BULK",
          "EXPRESS_NORDIC_0900_BULK",
          "PICKUP_PARCEL_BULK",
          "HOME_DELIVERY_PARCEL",
          "HOME_DELIVERY_MAILBOX",
        ]);
        $this->assertSame($this->class->getProductsCustomer('x'), []);
    }

    public function testProcessClientUrlVariables(): void
    {
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setEndPoint('x'));
        $this->assertInstanceOf(ClientsInterface::class, $this->class->processClientUrlVariables());
        $this->assertSame($this->class->getClientUrlVariables(), ['x']);
    }

    public function testCheckErrorsException(): void
    {
        $this->expectException(BringClientException::class);
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setResponse(new Response(200, ['X-Foo' => 'Bar'], 'test')));
        $this->class->checkErrors();
    }

    public function testCheckErrors(): void
    {
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setResponse(new Response(200, ['X-Foo' => 'Bar'], '{"q":"testing"}')));
        $this->assertInstanceOf(ClientsInterface::class, $this->class->checkErrors());
    }

    public function testProcessEntity(): void
    {
        $this->assertInstanceOf(ClientsInterface::class, $this->class->processEntity());
    }

    public function testSend(): void
    {
        $mock = new MockHandler([$this->class->getResponse()]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setClient($client));
        $this->assertInstanceOf(ClientsInterface::class, $this->class->send());
        $this->assertJsonStringEqualsJsonString($this->json, $this->class->toJson());
    }
}
