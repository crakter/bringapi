<?php

declare(strict_types=1);

namespace Bring\Api\Tests\Endpoint\Shipping;

use Bring\Api\Endpoint\Shipping\PriceRequest;
use Bring\Api\Enum\AdditionalService;
use Bring\Api\Enum\Country;
use Bring\Api\Enum\Language;
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

    /**
     * @param array<string, mixed> $body
     *
     * @return array<string, mixed>
     */
    private static function consignment(array $body): array
    {
        self::assertIsArray($body['consignments']);
        self::assertCount(1, $body['consignments']);
        $consignment = $body['consignments'][0];
        self::assertIsArray($consignment);

        /** @var array<string, mixed> $consignment */
        return $consignment;
    }

    public function testProductsSerialiseToBringNumericServiceCodes(): void
    {
        $c = self::consignment($this->request(Product::BUSINESS_PARCEL, Product::PICKUP_PARCEL)->toJsonBody());

        // Shipping Guide v2 prices by numeric code, not the v2 string name.
        self::assertSame([['id' => '5000'], ['id' => '5800']], $c['products']);
    }

    public function testExpressUsesNumericShippingGuideCode(): void
    {
        $c = self::consignment($this->request(Product::EXPRESS_NORDIC_0900)->toJsonBody());

        // 4850 = Business Parcel Express ("Pakke til bedrift ekspress"), the
        // express product this catalog uses — NOT the v2 string name, and NOT
        // the unrelated "Express Nordic 09:00" courier code 0335.
        self::assertSame([['id' => '4850']], $c['products']);
    }

    public function testUnmappedProductFallsBackToStringValue(): void
    {
        $c = self::consignment($this->request(Product::EXPRESS_INTERNATIONAL)->toJsonBody());

        self::assertSame([['id' => 'EXPRESS_INTERNATIONAL']], $c['products']);
    }

    public function testEveryPackageIsItsOwnEntry(): void
    {
        // The GET form priced 1, 2 and 20 packages alike; the POST body lists
        // each package, which is what makes Bring price all of them.
        $c = self::consignment((new PriceRequest(
            fromCountry: Country::NO,
            fromPostalCode: '1712',
            toCountry: Country::NO,
            toPostalCode: '0150',
            packages: [
                ['weightInGrams' => 1000, 'length' => 30, 'width' => 20, 'height' => 15],
                ['weightInGrams' => 2500],
                ['weightInGrams' => 400],
            ],
        ))->toJsonBody());

        self::assertSame([
            ['id' => '1', 'grossWeight' => 1000, 'length' => 30, 'width' => 20, 'height' => 15],
            ['id' => '2', 'grossWeight' => 2500],
            ['id' => '3', 'grossWeight' => 400],
        ], $c['packages']);
    }

    public function testRouteAndOptionsGoWhereTheSchemaPutsThem(): void
    {
        $body = (new PriceRequest(
            fromCountry: Country::NO,
            fromPostalCode: '1640',
            toCountry: Country::SE,
            toPostalCode: '65631',
            packages: [['weightInGrams' => 1000]],
            products: [Product::BUSINESS_PARCEL],
            additional: [AdditionalService::cases()[0]],
            language: Language::NORWEGIAN_BOKMAL,
            shippingDate: new \DateTimeImmutable('2026-11-09 07:05'),
            edi: true,
        ))->toJsonBody();
        $c = self::consignment($body);

        self::assertSame('NO', $c['fromCountryCode']);
        self::assertSame('1640', $c['fromPostalCode']);
        self::assertSame('SE', $c['toCountryCode']);
        self::assertSame('65631', $c['toPostalCode']);
        self::assertSame([['id' => AdditionalService::cases()[0]->value]], $c['additionalServices']);
        self::assertSame(['year' => '2026', 'month' => '11', 'day' => '9', 'hour' => '7', 'minute' => '05'], $c['shippingDate']);
        self::assertSame('NO', $body['language']);
        self::assertTrue($body['withPrice']);
        self::assertFalse($body['withExpectedDelivery']);
        self::assertTrue($body['edi']);
        self::assertFalse($body['postingAtPostoffice']);
    }

    public function testALanguageTheBodyCannotCarryIsLeftOut(): void
    {
        $body = (new PriceRequest(
            fromCountry: Country::NO,
            fromPostalCode: '1712',
            toCountry: Country::NO,
            toPostalCode: '0150',
            packages: [['weightInGrams' => 1000]],
            language: Language::FINNISH,
        ))->toJsonBody();

        self::assertArrayNotHasKey('language', $body);
    }

    public function testEndpointsCanForceTheirFlagOn(): void
    {
        $request = new PriceRequest(
            fromCountry: Country::NO,
            fromPostalCode: '1712',
            toCountry: Country::NO,
            toPostalCode: '0150',
            packages: [['weightInGrams' => 1000]],
            withPrice: false,
        );

        self::assertFalse($request->toJsonBody()['withPrice']);
        self::assertTrue($request->toJsonBody(withPrice: true)['withPrice']);
        self::assertTrue($request->toJsonBody(withExpectedDelivery: true)['withExpectedDelivery']);
    }
}
