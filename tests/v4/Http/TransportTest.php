<?php

declare(strict_types=1);

namespace Bring\Api\Tests\Http;

use Bring\Api\Auth\Credentials;
use Bring\Api\Auth\HeaderAuthorization;
use Bring\Api\Auth\NullAuthorization;
use Bring\Api\Endpoint\PostalCode\PostalCodeEndpoint;
use Bring\Api\Endpoint\Shipping\PriceEndpoint;
use Bring\Api\Endpoint\Shipping\PriceRequest;
use Bring\Api\Enum\Country;
use Bring\Api\Exception\BringApiException;
use Bring\Api\Exception\BringTransportException;
use Bring\Api\Http\HeaderNames;
use Bring\Api\Http\Transport;
use Bring\Api\Tests\Support\RecordingClient;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;

#[CoversClass(Transport::class)]
final class TransportTest extends TestCase
{
    private HttpFactory $factory;

    #[\Override]
    protected function setUp(): void
    {
        $this->factory = new HttpFactory();
    }

    public function testAttachesAuthHeadersToRequest(): void
    {
        $client = new RecordingClient([new Response(200, [], '{"valid":true,"result":"OSLO"}')]);
        $transport = new Transport(
            $client,
            $this->factory,
            $this->factory,
            $this->factory,
            new HeaderAuthorization(new Credentials('me@example.com', 'secretKey', 'https://example.com')),
        );

        $transport->send(new PostalCodeEndpoint('0150'));

        $req = $client->lastRequest();
        self::assertSame('me@example.com', $req->getHeaderLine(HeaderNames::AUTH_UID));
        self::assertSame('secretKey', $req->getHeaderLine(HeaderNames::AUTH_KEY));
    }

    public function testTestModeAttachesIndicator(): void
    {
        $client = new RecordingClient([new Response(200, [], '{}')]);
        $transport = (new Transport(
            $client,
            $this->factory,
            $this->factory,
            $this->factory,
            new NullAuthorization(),
        ))->withTestMode(true);

        $transport->send(new PostalCodeEndpoint('0150'));
        self::assertSame('true', $client->lastRequest()->getHeaderLine(HeaderNames::TEST_MODE));
    }

    public function testThrowsBringApiExceptionOnNon2xx(): void
    {
        $client = new RecordingClient([new Response(401, ['Content-Type' => 'application/json'], '{"errors":[{"code":"UNAUTHORIZED","description":"Invalid API key"}]}')]);
        $transport = new Transport($client, $this->factory, $this->factory, $this->factory, new NullAuthorization());

        try {
            $transport->send(new PostalCodeEndpoint('0150'));
            self::fail('expected BringApiException');
        } catch (BringApiException $e) {
            self::assertSame(401, $e->getStatusCode());
            self::assertCount(1, $e->getErrors());
            self::assertSame('UNAUTHORIZED', $e->getErrors()[0]->code);
            self::assertSame('Invalid API key', $e->getErrors()[0]->message);
            self::assertStringContainsString('HTTP 401', $e->getMessage());
            self::assertStringContainsString('UNAUTHORIZED', $e->getMessage());
        }
    }

    public function testApiExceptionMessageEmbedsFirstErrorButNotRawBody(): void
    {
        $client = new RecordingClient([new Response(400, ['Content-Type' => 'application/json'], '{"errors":[{"code":"BAD","description":"Missing field"}],"echo":"api_key=secretKey"}')]);
        $transport = new Transport($client, $this->factory, $this->factory, $this->factory, new NullAuthorization());

        try {
            $transport->send(new PostalCodeEndpoint('0150'));
            self::fail('expected BringApiException');
        } catch (BringApiException $e) {
            self::assertStringContainsString('BAD', $e->getMessage());
            self::assertStringNotContainsString('secretKey', $e->getMessage(), 'raw response body must not leak via getMessage');
            self::assertStringNotContainsString('api_key', $e->getMessage());
            self::assertNotNull($e->getResponse(), 'PSR-7 response remains accessible for explicit callers');
        }
    }

    public function testTransportExceptionWrapsPsr18Failure(): void
    {
        $networkFail = new class('boom') extends \RuntimeException implements ClientExceptionInterface {
        };
        $client = new RecordingClient([$networkFail]);
        $transport = new Transport($client, $this->factory, $this->factory, $this->factory, new NullAuthorization());

        $this->expectException(BringTransportException::class);
        $transport->send(new PostalCodeEndpoint('0150'));
    }

    public function testShippingPriceQueryBuildsBareRepeatedKeys(): void
    {
        $client = new RecordingClient([new Response(200, ['Content-Type' => 'application/json'], '{"Product":[]}')]);
        $transport = new Transport($client, $this->factory, $this->factory, $this->factory, new NullAuthorization());

        $request = new PriceRequest(
            fromCountry: Country::NO,
            fromPostalCode: '0150',
            toCountry: Country::NO,
            toPostalCode: '5003',
            packages: [['weightInGrams' => 1200], ['weightInGrams' => 800]],
        );
        $transport->send(new PriceEndpoint($request));

        $uri = (string) $client->lastRequest()->getUri();
        // Repeated weightInGrams without [0]/[1] subscripts (Bring's expected format).
        self::assertStringContainsString('weightInGrams=1200', $uri);
        self::assertStringContainsString('weightInGrams=800', $uri);
        self::assertStringNotContainsString('weightInGrams%5B0%5D=', $uri);
        self::assertStringNotContainsString('weightInGrams[0]=', $uri);
    }

    public function testPostalCodeRequestUrl(): void
    {
        $client = new RecordingClient([new Response(200, [], '{"valid":true,"result":"OSLO"}')]);
        $transport = new Transport($client, $this->factory, $this->factory, $this->factory, new NullAuthorization());

        $resp = $transport->send(new PostalCodeEndpoint('0150', Country::NO));

        self::assertTrue($resp->valid);
        self::assertSame('OSLO', $resp->city);
        $uri = (string) $client->lastRequest()->getUri();
        self::assertStringStartsWith('https://api.bring.com/shippingguide/api/postalCode.json', $uri);
        self::assertStringContainsString('pnr=0150', $uri);
        self::assertStringContainsString('country=NO', $uri);
        $accept = $client->lastRequest()->getHeaderLine('Accept');
        self::assertSame('application/json', $accept);
    }

    public function testTrackingApiUsesUpdatedV2Url(): void
    {
        $client = new RecordingClient([new Response(200, [], '{"consignmentSet":[]}')]);
        $transport = new Transport($client, $this->factory, $this->factory, $this->factory, new NullAuthorization());

        $transport->send(new \Bring\Api\Endpoint\Tracking\TrackEndpoint('TESTPACKAGE'));

        $uri = (string) $client->lastRequest()->getUri();
        self::assertStringStartsWith('https://tracking.bring.com/api/v2/tracking.json', $uri);
        self::assertStringContainsString('q=TESTPACKAGE', $uri);
    }

    public function testEnsureMethodAndAcceptOnRequest(): void
    {
        $client = new RecordingClient([new Response(200, [], '{}')]);
        $transport = new Transport($client, $this->factory, $this->factory, $this->factory, new NullAuthorization());

        $transport->send(new PostalCodeEndpoint('0150'));
        $req = $client->lastRequest();
        self::assertInstanceOf(RequestInterface::class, $req);
        self::assertSame('GET', $req->getMethod());
    }
}
