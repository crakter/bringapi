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
            products: array_values($products),
        );
    }

    public function testProductsSerialiseToBringNumericServiceCodes(): void
    {
        $q = $this->request(Product::BUSINESS_PARCEL, Product::PICKUP_PARCEL)->toQuery();

        // Shipping Guide v2 prices by numeric code, not the v2 string name.
        self::assertSame(['5000', '5800'], $q['product']);
    }

    public function testExpressUsesNumericShippingGuideCode(): void
    {
        $q = $this->request(Product::EXPRESS_NORDIC_0900)->toQuery();

        // 4850 = Business Parcel Express ("Pakke til bedrift ekspress"), the
        // express product this catalog uses — NOT the v2 string name, and NOT
        // the unrelated "Express Nordic 09:00" courier code 0335.
        self::assertSame(['4850'], $q['product']);
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
