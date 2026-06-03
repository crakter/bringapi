<?php

declare(strict_types=1);

namespace Bring\Api\Tests\Endpoint\Shipping;

use Bring\Api\Endpoint\Shipping\PriceRequest;
use Bring\Api\Enum\Country;
use Bring\Api\Enum\Product;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PriceRequest::class)]
final class PriceRequestTest extends TestCase
{
    private function request(Product ...$products): PriceRequest
    {
        return new PriceRequest(
            fromCountry: Country::NO,
            fromPostalCode: '1712',
            toCountry: Country::NO,
            toPostalCode: '0150',
            packages: [['weightInGrams' => 1000]],
            products: $products,
        );
    }

    public function testProductsSerialiseToBringNumericServiceCodes(): void
    {
        $q = $this->request(Product::BUSINESS_PARCEL, Product::PICKUP_PARCEL)->toQuery();

        // Shipping Guide v2 prices by numeric code, not the v2 string name.
        self::assertSame(['5000', '5800'], $q['product']);
    }

    public function testExpressNordicKeepsLeadingZeroAndUsesShippingGuideCode(): void
    {
        $q = $this->request(Product::EXPRESS_NORDIC_0900)->toQuery();

        // 0335 (Shipping Guide v2), NOT the string name and NOT the legacy
        // in-app code 4850 — and the leading zero must survive.
        self::assertSame(['0335'], $q['product']);
    }

    public function testUnmappedProductFallsBackToStringValue(): void
    {
        // No confirmed Shipping Guide numeric code: keep prior behaviour.
        $q = $this->request(Product::EXPRESS_INTERNATIONAL)->toQuery();

        self::assertSame(['EXPRESS_INTERNATIONAL'], $q['product']);
    }

    public function testNoProductParamWhenProductsEmpty(): void
    {
        self::assertArrayNotHasKey('product', $this->request()->toQuery());
    }
}
