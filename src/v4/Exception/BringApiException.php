<?php

declare(strict_types=1);

namespace Bring\Api\Exception;

use Psr\Http\Message\ResponseInterface;

/**
 * Bring returned a non-2xx response. The raw response body is NOT embedded
 * into ::getMessage() — Bring sometimes echoes credentials back. Callers
 * that want the body can call ::getResponse().
 */
final class BringApiException extends \RuntimeException implements BringException
{
    private readonly ?ResponseInterface $response;

    /** @param list<BringApiError> $errors */
    public function __construct(
        private readonly int $statusCode,
        private readonly array $errors,
        ?ResponseInterface $response = null,
        ?\Throwable $previous = null,
    ) {
        $this->response = $response;
        $first = $errors[0] ?? null;
        $msg = sprintf(
            'Bring API returned HTTP %d%s',
            $statusCode,
            $first === null
                ? '.'
                : sprintf(' (%s: %s)', $first->code ?: '-', $first->message),
        );
        parent::__construct($msg, 0, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /** @return list<BringApiError> */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    /**
     * Parse a Bring error envelope from a response. Bring returns one of:
     *   JSON: {"errors":[{"code":"X","description":"Y"}]}
     *         {"messages":[{"code":"X","description":"Y","messageType":"ERROR"}]}
     *   XML : <error><code>X</code><description>Y</description></error>
     * Falls back to a single synthetic error when parsing fails.
     */
    public static function fromResponse(ResponseInterface $response, ?\Throwable $previous = null): self
    {
        $status = $response->getStatusCode();
        $body = (string) $response->getBody();
        $errors = [];

        if ($body !== '') {
            $contentType = strtolower($response->getHeaderLine('Content-Type'));
            if (str_contains($contentType, 'json') || (str_starts_with(trim($body), '{') || str_starts_with(trim($body), '['))) {
                $decoded = json_decode($body, true);
                if (is_array($decoded)) {
                    foreach (($decoded['errors'] ?? $decoded['messages'] ?? []) as $err) {
                        if (!is_array($err)) {
                            continue;
                        }
                        $errors[] = new BringApiError(
                            (string) ($err['code'] ?? ''),
                            (string) ($err['description'] ?? $err['message'] ?? ''),
                            isset($err['messageType']) ? (string) $err['messageType'] : null,
                        );
                    }
                }
            } elseif (str_contains($contentType, 'xml') || str_starts_with(trim($body), '<')) {
                $previousErrors = libxml_use_internal_errors(true);
                try {
                    $xml = @simplexml_load_string($body);
                    if ($xml !== false) {
                        foreach ($xml->xpath('//error') ?: [] as $errNode) {
                            // SimpleXMLElement always returns a SimpleXMLElement for child
                            // accesses (empty when absent), so no null-coalesce is needed.
                            $errors[] = new BringApiError(
                                (string) $errNode->code,
                                (string) $errNode->description,
                                null,
                            );
                        }
                    }
                } finally {
                    libxml_clear_errors();
                    libxml_use_internal_errors($previousErrors);
                }
            }
        }

        if ($errors === []) {
            // No recognised error envelope. Surface a snippet of the raw body
            // so the actual Bring rejection reason isn't lost — without this,
            // unfamiliar 4xx shapes (plain text, HTML, JSON envelopes other
            // than errors/messages) collapse to "no parsable error body" and
            // the operator has nothing to act on. The body is redacted and
            // truncated; the parseable-envelope path is unchanged so the
            // "Bring sometimes echoes credentials" guarantee on getMessage()
            // still holds for the case it was written for.
            $snippet = self::redactBodySnippet($body);
            $errors[] = new BringApiError(
                '',
                $snippet === ''
                    ? sprintf('HTTP %d (empty error body)', $status)
                    : sprintf('HTTP %d: %s', $status, $snippet),
                null,
            );
        }

        return new self($status, $errors, $response, $previous);
    }

    /**
     * Trim, single-line, truncate and redact obvious credential-shaped values
     * from a response body so it's safe to embed in an exception message.
     */
    private static function redactBodySnippet(string $body): string
    {
        $body = trim($body);
        if ($body === '') {
            return '';
        }

        // Collapse whitespace so multi-line HTML/JSON doesn't blow up logs.
        $body = (string) preg_replace('/\s+/u', ' ', $body);

        // Redact common credential shapes: JSON "key":"value", form key=value,
        // and Authorization-style header values. Match key names case-insensitively.
        $sensitive = '(?:api[_-]?key|apikey|x-mybring-api-key|authorization|password|secret|token)';
        $body = (string) preg_replace(
            '/"(' . $sensitive . ')"\s*:\s*"[^"]*"/i',
            '"$1":"[REDACTED]"',
            $body,
        );
        $body = (string) preg_replace(
            '/\b(' . $sensitive . ')\s*=\s*[^&\s"\']+/i',
            '$1=[REDACTED]',
            $body,
        );

        $limit = 500;
        if (mb_strlen($body) > $limit) {
            $body = mb_substr($body, 0, $limit) . '…';
        }

        return $body;
    }
}
