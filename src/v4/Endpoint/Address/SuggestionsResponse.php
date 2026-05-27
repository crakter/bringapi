<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Address;

final class SuggestionsResponse
{
    /**
     * @param list<PostalCodeLookupResponse> $matches
     * @param array<mixed, mixed>            $raw
     */
    public function __construct(
        public readonly array $matches,
        public readonly array $raw,
    ) {
    }

    /** @param array<mixed, mixed> $decoded */
    public static function fromArray(array $decoded): self
    {
        $matches = [];
        $items = $decoded['postal_codes'] ?? $decoded['suggestions'] ?? [];
        if (is_array($items)) {
            foreach ($items as $item) {
                if (is_array($item)) {
                    $matches[] = PostalCodeLookupResponse::fromArray($item);
                }
            }
        }

        return new self($matches, $decoded);
    }
}
