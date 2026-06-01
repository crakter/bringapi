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
            $errors[] = new BringApiError('', sprintf('HTTP %d (no parsable error body)', $status), null);
        }

        return new self($status, $errors, $response, $previous);
    }
}
