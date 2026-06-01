<?php

declare(strict_types=1);

namespace Bring\Api\Auth;

/**
 * Immutable Bring credentials.
 *
 * The API key is wrapped in #[\SensitiveParameter] so PHP 8.2+ scrubs it from
 * stack traces. __debugInfo() also masks it: print_r/var_dump on this object
 * yields a SHA-256 fingerprint, never the raw key.
 */
final class Credentials
{
    public function __construct(
        private readonly string $uid,
        #[\SensitiveParameter]
        private readonly string $apiKey,
        private readonly ?string $clientUrl = null,
    ) {
        if ($uid === '') {
            throw new \InvalidArgumentException('Bring credentials: uid (Mybring login email) must not be empty.');
        }
        if ($apiKey === '') {
            throw new \InvalidArgumentException('Bring credentials: apiKey must not be empty.');
        }
    }

    public function getUid(): string
    {
        return $this->uid;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getClientUrl(): ?string
    {
        return $this->clientUrl;
    }

    /** Stable, non-secret identifier for logs. */
    public function getApiKeyFingerprint(): string
    {
        return substr(hash('sha256', $this->apiKey), 0, 8);
    }

    /** @return array<string, string|null> */
    public function __debugInfo(): array
    {
        return [
            'uid' => $this->uid,
            'clientUrl' => $this->clientUrl,
            'apiKeyFingerprint' => $this->getApiKeyFingerprint(),
        ];
    }
}
