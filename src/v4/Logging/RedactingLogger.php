<?php

declare(strict_types=1);

namespace Bring\Api\Logging;

use Bring\Api\Http\HeaderNames;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Stringable;

/**
 * PSR-3 decorator that strips Bring auth headers and the raw API key from
 * messages and context before forwarding to the wrapped logger.
 *
 * Sensitive header values are replaced with "***redacted***" in any array
 * keyed by 'headers' (or 'request_headers' / 'response_headers') anywhere in
 * the context tree. Free-form scalar substrings matching the credentials are
 * also masked.
 */
final class RedactingLogger extends AbstractLogger
{
    private const MASK = '***redacted***';
    private const HEADER_KEYS = ['headers', 'request_headers', 'response_headers'];

    /** @var list<string> Lower-cased header names to mask. */
    private readonly array $redactableHeaders;

    /** @var list<string> Literal credential strings to mask everywhere. */
    private array $literalSecrets;

    /**
     * @param array<int, string> $extraSecrets additional literal strings (e.g. API key, UID) to mask in free text
     */
    public function __construct(
        private readonly LoggerInterface $inner,
        array $extraSecrets = [],
    ) {
        $this->redactableHeaders = array_map('strtolower', HeaderNames::REDACTABLE);
        $this->literalSecrets = array_values(array_filter($extraSecrets, static fn ($v) => is_string($v) && $v !== ''));
    }

    public function withSecret(string $secret): self
    {
        if ($secret === '' || in_array($secret, $this->literalSecrets, true)) {
            return $this;
        }
        $clone = clone $this;
        $clone->literalSecrets[] = $secret;

        return $clone;
    }

    public function log($level, string|Stringable $message, array $context = []): void
    {
        $msg = $this->maskString((string) $message);
        $ctx = $this->maskContext($context);
        $this->inner->log($level, $msg, $ctx);
    }

    /** @param array<mixed, mixed> $context @return array<mixed, mixed> */
    private function maskContext(array $context): array
    {
        foreach ($context as $key => $value) {
            if (is_string($key) && in_array(strtolower($key), self::HEADER_KEYS, true) && is_array($value)) {
                $context[$key] = $this->maskHeaders($value);
                continue;
            }
            if (is_array($value)) {
                $context[$key] = $this->maskContext($value);
            } elseif (is_string($value)) {
                $context[$key] = $this->maskString($value);
            }
        }

        return $context;
    }

    /** @param array<mixed, mixed> $headers @return array<mixed, mixed> */
    private function maskHeaders(array $headers): array
    {
        foreach ($headers as $name => $value) {
            if (is_string($name) && in_array(strtolower($name), $this->redactableHeaders, true)) {
                $headers[$name] = self::MASK;
            } elseif (is_array($value)) {
                $headers[$name] = $this->maskHeaders($value);
            } elseif (is_string($value)) {
                $headers[$name] = $this->maskString($value);
            }
        }

        return $headers;
    }

    private function maskString(string $s): string
    {
        if ($this->literalSecrets === []) {
            return $s;
        }

        return str_replace($this->literalSecrets, self::MASK, $s);
    }
}
