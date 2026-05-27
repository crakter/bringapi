<?php

declare(strict_types=1);

namespace Bring\Api\Tests\Auth;

use Bring\Api\Auth\Credentials;
use Bring\Api\Exception\InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Credentials::class)]
final class CredentialsTest extends TestCase
{
    private const KEY = 'sk-live-1234abcd-very-secret';

    public function testStoresAndReadsValues(): void
    {
        $c = new Credentials('me@example.com', self::KEY, 'https://example.com');
        self::assertSame('me@example.com', $c->getUid());
        self::assertSame(self::KEY, $c->getApiKey());
        self::assertSame('https://example.com', $c->getClientUrl());
    }

    public function testFingerprintIsStableAndShort(): void
    {
        $c = new Credentials('me@example.com', self::KEY);
        $fp = $c->getApiKeyFingerprint();
        self::assertSame(8, strlen($fp));
        self::assertMatchesRegularExpression('/^[0-9a-f]{8}$/', $fp);
        self::assertSame($fp, (new Credentials('other@example.com', self::KEY))->getApiKeyFingerprint(), 'fingerprint depends only on the key');
    }

    public function testDebugInfoOmitsRawKey(): void
    {
        $c = new Credentials('me@example.com', self::KEY, 'https://example.com');
        $info = $c->__debugInfo();
        self::assertArrayNotHasKey('apiKey', $info);
        self::assertSame($c->getApiKeyFingerprint(), $info['apiKeyFingerprint']);

        $rendered = print_r($c, true);
        self::assertStringNotContainsString(self::KEY, $rendered, 'print_r must not expose the API key');
    }

    public function testEmptyUidRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Credentials('', self::KEY);
    }

    public function testEmptyKeyRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Credentials('me@example.com', '');
    }
}
