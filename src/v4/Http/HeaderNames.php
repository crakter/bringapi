<?php

declare(strict_types=1);

namespace Bring\Api\Http;

/**
 * Canonical Bring HTTP header names.
 *
 * Casing matches https://developer.bring.com/api/authentication/ exactly. HTTP
 * headers are case-insensitive per RFC 7230, but matching the docs keeps logs
 * and stack traces grep-friendly.
 */
final class HeaderNames
{
    public const AUTH_UID = 'X-Mybring-API-Uid';
    public const AUTH_KEY = 'X-Mybring-API-Key';
    public const CLIENT_URL = 'X-Bring-Client-URL';
    public const TEST_MODE = 'X-Bring-Test-Indicator';

    /** Headers that must never appear in logs in plaintext. */
    public const SENSITIVE = [self::AUTH_KEY];

    /** Headers a redacting logger should mask entirely. */
    public const REDACTABLE = [
        self::AUTH_KEY,
        self::AUTH_UID,
        self::CLIENT_URL,
        'Authorization',
    ];

    private function __construct()
    {
    }
}
