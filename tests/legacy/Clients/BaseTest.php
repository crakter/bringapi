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
use Crakter\BringApi\DefaultData\ReturnFileContentTypes;
use Crakter\BringApi\DefaultData\ReturnFileTypes;
use Crakter\BringApi\DefaultData\HttpMethods;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Crakter\BringApi\Entity\ApiEntityInterface;
use Crakter\BringApi\Entity\TrackingEntity;

class BaseTest extends TestCase
{
    private $class;

    public function setUp(): void
    {
        $this->class = (new Booking\BookShipment);
    }

    public function testConstruct(): void
    {
        $this->assertInstanceOf(ClientsInterface::class, (new Booking\BookShipment(new TrackingEntity(), new Authorization())));
    }

    public function testSetAcceptLanguage(): void
    {
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setAcceptLanguage('no'));
        $this->assertSame($this->class->getOptions(), ['headers' => ['Accept-Language' => 'no']]);
    }

    public function testSetHeader(): void
    {
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setHeader('testing', 'name'));
        $this->assertSame($this->class->getOptions(), ['headers' => ['testing' => 'name']]);
    }

    public function testSetOptionsQuery(): void
    {
        $array = [
            'schemaVersion' => 1,
            'testIndicator' => true,
            'customerNumber' => 'x',
        ];
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setOptionsQuery($array));
        $this->assertSame($this->class->getOptions(), ['query' => 'schemaVersion=1&testIndicator=true&customerNumber=x']);
    }

    public function testSetOptionsJson(): void
    {
        $array = [
            'schemaVersion' => 1,
            'testIndicator' => true,
            'customerNumber' => 'x',
        ];
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setOptionsJson($array));
        $this->assertSame($this->class->getOptions(), ['json' => $array]);
    }

    public function testSetGetOptions(): void
    {
        $array = ['json' => [
            'schemaVersion' => 1,
            'testIndicator' => true,
            'customerNumber' => 'x',
        ]];
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setOptions($array));
        $this->assertSame($this->class->getOptions(), $array);
    }

    public function testSetReturnXml(): void
    {
        $array = ['headers' => [
            'Accept' => ReturnFileContentTypes::XML,
            'Content-type' => ReturnFileContentTypes::XML,
        ]];
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setReturnXml());
        $this->assertSame($this->class->getOptions(), $array);
        $this->assertSame($this->class->getEndPoint(), ReturnFileTypes::XML);
    }

    public function testSetReturnPng(): void
    {
        $array = ['headers' => [
            'Accept' => ReturnFileContentTypes::PNG,
            'Content-type' => ReturnFileContentTypes::PNG,
        ]];
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setReturnPng());
        $this->assertSame($this->class->getOptions(), $array);
        $this->assertSame($this->class->getEndPoint(), ReturnFileTypes::PNG);
    }

    public function testSetReturnXls(): void
    {
        $array = ['headers' => [
            'Accept' => ReturnFileContentTypes::XLS,
            'Content-type' => ReturnFileContentTypes::XLS,
        ]];
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setReturnXls());
        $this->assertSame($this->class->getOptions(), $array);
        $this->assertSame($this->class->getEndPoint(), ReturnFileTypes::XLS);
    }

    public function testSetReturnHtml(): void
    {
        $array = ['headers' => [
            'Accept' => ReturnFileContentTypes::HTML,
            'Content-type' => ReturnFileContentTypes::HTML,
        ]];
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setReturnHtml());
        $this->assertSame($this->class->getOptions(), $array);
        $this->assertSame($this->class->getEndPoint(), ReturnFileTypes::HTML);
    }

    public function testSetReturnJson(): void
    {
        $array = ['headers' => [
            'Accept' => ReturnFileContentTypes::JSON,
            'Content-type' => ReturnFileContentTypes::JSON,
        ]];
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setReturnJson());
        $this->assertSame($this->class->getOptions(), $array);
        $this->assertSame($this->class->getEndPoint(), ReturnFileTypes::JSON);
    }

    public function testSetGetEndPoint(): void
    {
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setEndPoint(ReturnFileTypes::JSON));
        $this->assertSame($this->class->getEndPoint(), ReturnFileTypes::JSON);
    }

    public function testSetGetClientUrl(): void
    {
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setClientUrl('x'));
        $this->assertSame($this->class->getClientUrl(), 'x');
    }

    public function testSetGetIsFullAddress(): void
    {
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setIsFullAddress(true));
        $this->assertSame($this->class->getIsFullAddress(), true);
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setClientUrl('x', false));
        $this->assertSame($this->class->getIsFullAddress(), false);
    }

    public function testSetGetClientUrlVariables(): void
    {
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setClientUrlVariables('test', 'testing', 'end'));
        $this->assertSame($this->class->getClientUrlVariables(), ['test', 'testing', 'end']);
    }

    public function testSetGetResponse(): void
    {
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setResponse(new Response(200, ['X-Foo' => 'Bar'])));
        $this->assertInstanceOf(Response::class, $this->class->getResponse());
    }

    public function testToJson(): void
    {
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setEndPoint(ReturnFileTypes::XML));
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setResponse(new Response(200, ['X-Foo' => 'Bar'], '<?xml version="1.0"?><Response><q>testing</q></Response>')));
        $this->assertJsonStringEqualsJsonString('{"q":"testing"}', $this->class->toJson());
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setEndPoint(ReturnFileTypes::JSON));
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setResponse(new Response(200, ['X-Foo' => 'Bar'], '{"q":"testing"}')));
        $this->assertJsonStringEqualsJsonString('{"q":"testing"}', $this->class->toJson());
        if (class_exists('PHPExcel_IOFactory')) {
            $file = file_get_contents(dirname(__DIR__).'/Data/test.xls');
            $this->assertInstanceOf(ClientsInterface::class, $this->class->setEndPoint(ReturnFileTypes::XLS));
            $this->assertInstanceOf(ClientsInterface::class, $this->class->setResponse(new Response(200, ['X-Foo' => 'Bar'], $file)));
            $this->assertJsonStringEqualsJsonString('{"q":"testing"}', $this->class->toJson());
        }
    }

    public function testIsJson(): void
    {
        $this->assertTrue($this->class->isJson('{"q":"testing"}'));
        $this->assertFalse($this->class->isJson('{"q":"testing}'));
    }

    public function testToArray(): void
    {
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setEndPoint(ReturnFileTypes::JSON));
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setResponse(new Response(200, ['X-Foo' => 'Bar'], '{"q":"testing"}')));
        $this->assertArrayHasKey('q', $this->class->toArray());
    }

    public function testToXml(): void
    {
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setEndPoint(ReturnFileTypes::JSON));
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setResponse(new Response(200, ['X-Foo' => 'Bar'], '{"q":{"p":"testing"}}')));
        $this->assertSame('<?xml version="1.0"?><root><q><p>testing</p></q></root>', str_replace(["\r", "\n", '  '], '', $this->class->toXml('root')));
    }

    public function testGetSetHttpMethod(): void
    {
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setGet());
        $this->assertSame(HttpMethods::GET, $this->class->getHttpMethod());
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setPost());
        $this->assertSame(HttpMethods::POST, $this->class->getHttpMethod());
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setHttpMethod(HttpMethods::POST));
        $this->assertSame(HttpMethods::POST, $this->class->getHttpMethod());
    }

    public function testSetGetAuthorizationModule(): void
    {
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setAuthorizationModule(new Authorization()));
        $this->assertInstanceOf(AuthorizationInterface::class, $this->class->getAuthorizationModule());
    }

    public function testSetGetApiEntity(): void
    {
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setApiEntity(new TrackingEntity()));
        $this->assertInstanceOf(ApiEntityInterface::class, $this->class->getApiEntity());
    }

    public function testSetGetAlternativeAuthorizedUrl(): void
    {
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setAlternativeAuthorizedUrl('x'));
        $this->assertSame('x', $this->class->getAlternativeAuthorizedUrl());
    }

    public function testSetGetClient(): void
    {
        $this->assertInstanceOf(ClientsInterface::class, $this->class->setClient(new Client()));
        $this->assertInstanceOf(ClientInterface::class, $this->class->getClient());
    }
}
