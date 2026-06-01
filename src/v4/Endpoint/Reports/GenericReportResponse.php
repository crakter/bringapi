<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Reports;

/**
 * Generic typed wrapper for the list-style Reports endpoints. The Reports API
 * returns very different payloads per endpoint and per customer; we keep the
 * raw decoded body available and let the caller inspect it directly.
 */
final class GenericReportResponse
{
    /** @param array<mixed, mixed> $raw */
    public function __construct(public readonly array $raw)
    {
    }

    /** @param array<mixed, mixed> $decoded */
    public static function fromArray(array $decoded): self
    {
        return new self($decoded);
    }
}
