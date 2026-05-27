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
        $price = $a['price']['listPrice'] ?? $a['price'] ?? [];
        $expected = $a['expectedDelivery']['workingDays']
            ?? $a['expectedDelivery'][0]['workingDays']
            ?? null;

        return new self(
            productId: (string) ($a['productId'] ?? $a['id'] ?? ''),
            priceWithVat: isset($price['amountWithVAT']['value']) ? (float) $price['amountWithVAT']['value']
                : (isset($price['priceWithAdditionalServices']['amountWithVAT']['value']) ? (float) $price['priceWithAdditionalServices']['amountWithVAT']['value'] : null),
            priceWithoutVat: isset($price['amountWithoutVAT']['value']) ? (float) $price['amountWithoutVAT']['value']
                : (isset($price['priceWithAdditionalServices']['amountWithoutVAT']['value']) ? (float) $price['priceWithAdditionalServices']['amountWithoutVAT']['value'] : null),
            vat: isset($price['vat']['value']) ? (float) $price['vat']['value'] : null,
            currency: isset($price['amountWithVAT']['currencyIdentificationCode'])
                ? (string) $price['amountWithVAT']['currencyIdentificationCode']
                : (isset($price['currencyIdentificationCode']) ? (string) $price['currencyIdentificationCode'] : null),
            expectedDeliveryDays: $expected !== null ? (int) $expected : null,
            raw: $a,
        );
    }
}
