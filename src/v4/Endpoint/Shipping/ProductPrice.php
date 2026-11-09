<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Shipping;

/** A single product row from the Shipping Guide price/products response. */
final class ProductPrice
{
    public function __construct(
        public readonly string $productId,
        public readonly ?float $priceWithVat,
        public readonly ?float $priceWithoutVat,
        public readonly ?float $vat,
        public readonly ?string $currency,
        public readonly ?int $expectedDeliveryDays,
        /** @var array<mixed, mixed> */
        public readonly array $raw,
    ) {
    }

    /** @param array<mixed, mixed> $a */
    public static function fromArray(array $a): self
    {
        $expected = $a['expectedDelivery']['workingDays']
            ?? $a['expectedDelivery'][0]['workingDays']
            ?? null;

        $block = $a['price']['listPrice'] ?? $a['price']['netPrice'] ?? $a['price'] ?? [];
        $block = \is_array($block) ? $block : [];
        // The POST Shipping Guide nests the amounts one level down, as plain
        // strings: listPrice.priceWithAdditionalServices.amountWithVAT = "123.45".
        // Older answers carried them flat, each as {"value": …}.
        $nested = $block['priceWithAdditionalServices'] ?? $block['priceWithoutAdditionalServices'] ?? null;
        $amounts = \is_array($nested) ? $nested : $block;

        $currency = $block['currencyCode']
            ?? $amounts['amountWithVAT']['currencyIdentificationCode']
            ?? $amounts['currencyIdentificationCode']
            ?? $block['currencyIdentificationCode']
            ?? null;

        return new self(
            productId: (string) ($a['productId'] ?? $a['id'] ?? ''),
            priceWithVat: self::amount($amounts['amountWithVAT'] ?? null),
            priceWithoutVat: self::amount($amounts['amountWithoutVAT'] ?? null),
            vat: self::amount($amounts['vat'] ?? null),
            currency: \is_scalar($currency) ? (string) $currency : null,
            expectedDeliveryDays: is_numeric($expected) ? (int) $expected : null,
            raw: $a,
        );
    }

    /** An amount as Bring writes it: a number, a numeric string, or {"value": …}. */
    private static function amount(mixed $raw): ?float
    {
        if (\is_array($raw)) {
            $raw = $raw['value'] ?? null;
        }

        return is_numeric($raw) ? (float) $raw : null;
    }
}
