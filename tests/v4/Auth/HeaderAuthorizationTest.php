<?php

declare(strict_types=1);

namespace Bring\Api\Tests\Auth;

use Bring\Api\Auth\Credentials;
use Bring\Api\Auth\HeaderAuthorization;
use Bring\Api\Auth\NullAuthorization;
use Bring\Api\Http\HeaderNames;
use GuzzleHttp\Psr7\HttpFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(HeaderAuthorization::class)]
#[CoversClass(NullAuthorization::class)]
final class HeaderAuthorizationTest extends TestCase
{
    public function testAppliesCanonicalCasedHeaders(): void
    {
        $auth = new HeaderAuthorization(new Credentials('me@example.com', 'secretKey', 'https://example.com'));
        $factory = new HttpFactory();
        $req = $factory->createRequest('GET', 'https://api.bring.com/');

        $signed = $auth->applyTo($req);

        self::assertSame('me@example.com', $signed->getHeaderLine(HeaderNames::AUTH_UID));
        self::assertSame('secretKey', $signed->getHeaderLine(HeaderNames::AUTH_KEY));
        self::assertSame('https://example.com', $signed->getHeaderLine(HeaderNames::CLIENT_URL));

        // Casing matches Bring's docs exactly.
        self::assertArrayHasKey('X-Mybring-API-Uid', $signed->getHeaders());
        self::assertArrayHasKey('X-Mybring-API-Key', $signed->getHeaders());
        self::assertArrayNotHasKey('X-MyBring-API-Uid', $signed->getHeaders(), 'legacy mis-cased header must not leak');
    }

    public function testClientUrlOmittedWhenAbsent(): void
    {
        $auth = new HeaderAuthorization(new Credentials('me@example.com', 'k'));
        $factory = new HttpFactory();
        $signed = $auth->applyTo($factory->createRequest('GET', 'https://api.bring.com/'));

        self::assertFalse($signed->hasHeader(HeaderNames::CLIENT_URL));
    }

    public function testNullAuthAddsNoHeaders(): void
    {
        $factory = new HttpFactory();
        $req = $factory->createRequest('GET', 'https://api.bring.com/');
        $out = (new NullAuthorization())->applyTo($req);

        self::assertSame($req->getHeaders(), $out->getHeaders());
        self::assertFalse((new NullAuthorization())->isAuthenticated());
    }
}
