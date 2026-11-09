<?php

declare(strict_types=1);

namespace Bring\Api\Tests\Endpoint\Shipping;

use Bring\Api\Endpoint\Shipping\ProductPrice;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ProductPrice::class)]
final class ProductPriceTest extends TestCase
{
    public function testReadsThePostAnswersNestedStringAmounts(): void
    {
        // The shape `POST /shippingguide/api/v2/products` answers with.
        $p = ProductPrice::fromArray([
            'id' => '4850',
            'price' => [
                'listPrice' => [
                    'priceWithoutAdditionalServices' => ['amountWithoutVAT' => '300.00', 'vat' => '75.00', 'amountWithVAT' => '375.00'],
                    'priceWithAdditionalServices' => ['amountWithoutVAT' => '320.00', 'vat' => '80.00', 'amountWithVAT' => '400.00'],
                    'currencyCode' => 'NOK',
                ],
            ],
            'expectedDelivery' => ['workingDays' => '1'],
        ]);

        self::assertSame('4850', $p->productId);
        self::assertSame(400.0, $p->priceWithVat);
        self::assertSame(320.0, $p->priceWithoutVat);
        self::assertSame(80.0, $p->vat);
        self::assertSame('NOK', $p->currency);
        self::assertSame(1, $p->expectedDeliveryDays);
    }

    public function testFallsBackToThePriceWithoutAdditionalServices(): void
    {
        $p = ProductPrice::fromArray([
            'id' => '5800',
            'price' => ['listPrice' => [
                'priceWithoutAdditionalServices' => ['amountWithoutVAT' => '93.89', 'vat' => '23.47', 'amountWithVAT' => '117.36'],
                'currencyCode' => 'NOK',
            ]],
        ]);

        self::assertSame(117.36, $p->priceWithVat);
        self::assertSame(93.89, $p->priceWithoutVat);
    }

    public function testStillReadsTheOlderFlatValueShape(): void
    {
        $p = ProductPrice::fromArray([
            'productId' => 'SERVICEPAKKE',
            'price' => [
                'amountWithVAT' => ['value' => 100, 'currencyIdentificationCode' => 'NOK'],
                'amountWithoutVAT' => ['value' => 80],
                'vat' => ['value' => 20],
            ],
        ]);

        self::assertSame(100.0, $p->priceWithVat);
        self::assertSame(80.0, $p->priceWithoutVat);
        self::assertSame(20.0, $p->vat);
        self::assertSame('NOK', $p->currency);
    }

    public function testAProductWithoutAPriceHasNone(): void
    {
        $p = ProductPrice::fromArray(['id' => '5800', 'errors' => [['code' => 'INVALID_ARGUMENT']]]);

        self::assertNull($p->priceWithVat);
        self::assertNull($p->priceWithoutVat);
        self::assertNull($p->currency);
    }
}
