<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Shipping;

use Bring\Api\Enum\AdditionalService;
use Bring\Api\Enum\Country;
use Bring\Api\Enum\Language;
use Bring\Api\Enum\Product;
use Bring\Api\Exception\InvalidArgumentException;

/**
 * Inputs for Shipping Guide v2 (/products, /products/price, /products/expectedDelivery).
 *
 * Bring's three product endpoints take the same query parameters; this DTO is
 * shared by all three. Construct it once and pass to whichever endpoint you need.
 *
 * Builder-style {@see with*()} mutators return a new instance — the DTO is
 * immutable, so you can safely cache and re-use a partially-built request.
 */
final class PriceRequest
{
    /**
     * @param list<Product>           $products             empty = all available
     * @param list<AdditionalService> $additional
     * @param array<int, array{weightInGrams:int, length?:int, width?:int, height?:int}> $packages at least one
     */
    public function __construct(
        public readonly Country $fromCountry,
        public readonly string $fromPostalCode,
        public readonly Country $toCountry,
        public readonly string $toPostalCode,
        public readonly array $packages,
        public readonly array $products = [],
        public readonly array $additional = [],
        public readonly ?Language $language = null,
        public readonly ?string $clientId = null,
        public readonly ?\DateTimeInterface $shippingDate = null,
        public readonly bool $withPrice = true,
        public readonly bool $withExpectedDelivery = false,
        public readonly bool $withGuiInformation = false,
        public readonly bool $edi = false,
        public readonly bool $postingAtPostOffice = false,
        public readonly ?string $pid = null,
    ) {
        if ($packages === []) {
            throw new InvalidArgumentException('PriceRequest: at least one package must be provided.');
        }
        foreach ($packages as $i => $pkg) {
            if ($pkg['weightInGrams'] < 1) {
                throw new InvalidArgumentException(sprintf('PriceRequest: packages[%d].weightInGrams must be a positive integer.', $i));
            }
        }
        if ($fromPostalCode === '' || $toPostalCode === '') {
            throw new InvalidArgumentException('PriceRequest: postal codes must not be empty.');
        }
    }

    /**
     * Languages the POST body's `language` field accepts. Finnish is not one
     * of them, so a Finnish request leaves the field out and Bring answers in
     * its default (Norwegian).
     */
    private const BODY_LANGUAGES = ['NO', 'SV', 'DA', 'EN'];

    /**
     * The JSON body for `POST /shippingguide/api/v2/products`, the Shipping
     * Guide request Bring documents. Every package is its own entry in
     * `consignments[0].packages`, so a request of three packages is priced as
     * three packages.
     *
     * `clientId` and `pid` have no field in the POST body and are not sent.
     *
     * @param bool|null $withPrice            overrides {@see $withPrice} (the price endpoint forces it on)
     * @param bool|null $withExpectedDelivery overrides {@see $withExpectedDelivery} (the delivery-time endpoint forces it on)
     *
     * @return array<string, mixed>
     */
    public function toJsonBody(?bool $withPrice = null, ?bool $withExpectedDelivery = null): array
    {
        $packages = [];
        foreach (array_values($this->packages) as $i => $pkg) {
            $package = ['id' => (string) ($i + 1), 'grossWeight' => (int) $pkg['weightInGrams']];
            foreach (['length', 'width', 'height'] as $dimension) {
                if (isset($pkg[$dimension])) {
                    $package[$dimension] = (int) $pkg[$dimension];
                }
            }
            $packages[] = $package;
        }

        $consignment = [
            'id' => '1',
            'fromCountryCode' => $this->fromCountry->value,
            'fromPostalCode' => $this->fromPostalCode,
            'toCountryCode' => $this->toCountry->value,
            'toPostalCode' => $this->toPostalCode,
            'packages' => $packages,
            // Shipping Guide v2 prices by Bring's numeric service code, not the
            // v2 string name. Fall back to the string value only for products
            // without a confirmed numeric code.
            'products' => array_map(
                static fn (Product $p): array => ['id' => $p->shippingGuideCode() ?? $p->value],
                $this->products,
            ),
        ];
        if ($this->additional !== []) {
            $consignment['additionalServices'] = array_map(
                static fn (AdditionalService $a): array => ['id' => $a->value],
                $this->additional,
            );
        }
        if ($this->shippingDate !== null) {
            $consignment['shippingDate'] = [
                'year' => $this->shippingDate->format('Y'),
                'month' => $this->shippingDate->format('n'),
                'day' => $this->shippingDate->format('j'),
                'hour' => $this->shippingDate->format('G'),
                'minute' => $this->shippingDate->format('i'),
            ];
        }

        $body = ['consignments' => [$consignment]];
        if ($this->language !== null && \in_array(strtoupper($this->language->value), self::BODY_LANGUAGES, true)) {
            $body['language'] = strtoupper($this->language->value);
        }
        $body['withPrice'] = $withPrice ?? $this->withPrice;
        $body['withExpectedDelivery'] = $withExpectedDelivery ?? $this->withExpectedDelivery;
        $body['withGuiInformation'] = $this->withGuiInformation;
        $body['edi'] = $this->edi;
        $body['postingAtPostoffice'] = $this->postingAtPostOffice;

        return $body;
    }

    /**
     * Query parameters for the old GET form of the Shipping Guide.
     *
     * @deprecated Bring documents only `POST /shippingguide/api/v2/products`, and
     *             the GET form priced a multi-package request as one package
     *             whichever way the packages were written. The endpoints send
     *             {@see toJsonBody()} now; this is kept for callers that built
     *             their own URL from it.
     *
     * @return array<string, mixed>
     */
    public function toQuery(): array
    {
        $q = [
            'fromCountry' => $this->fromCountry->value,
            'fromPostalCode' => $this->fromPostalCode,
            'toCountry' => $this->toCountry->value,
            'toPostalCode' => $this->toPostalCode,
        ];

        // Shipping Guide v2 addresses each package by an index *suffix* on the
        // parameter name (weightInGrams0, length0, ..., weightInGrams1, ...) —
        // not by a repeated bare key. A repeated `weightInGrams` is collapsed to
        // a single value server-side, so every multi-package request was priced
        // as one package. A lone package keeps the unsuffixed names, which is
        // what the API documents for the single-package case.
        $packages = array_values($this->packages);
        $multiple = \count($packages) > 1;
        foreach ($packages as $i => $pkg) {
            $suffix = $multiple ? (string) $i : '';
            $q['weightInGrams'.$suffix] = (int) $pkg['weightInGrams'];
            if (isset($pkg['length'])) {
                $q['length'.$suffix] = (int) $pkg['length'];
            }
            if (isset($pkg['width'])) {
                $q['width'.$suffix] = (int) $pkg['width'];
            }
            if (isset($pkg['height'])) {
                $q['height'.$suffix] = (int) $pkg['height'];
            }
        }

        if ($this->products !== []) {
            // Shipping Guide v2 prices by Bring's numeric service code, not the
            // v2 string name (sending EXPRESS_NORDIC_0900 et al. yields an empty
            // product list / "no price"). Fall back to the string value only for
            // products without a confirmed numeric code.
            $q['product'] = array_map(
                static fn (Product $p): string => $p->shippingGuideCode() ?? $p->value,
                $this->products,
            );
        }
        if ($this->additional !== []) {
            $q['additional'] = array_map(static fn (AdditionalService $a): string => $a->value, $this->additional);
        }
        if ($this->language !== null) {
            $q['language'] = $this->language->value;
        }
        if ($this->clientId !== null) {
            $q['clientId'] = $this->clientId;
        }
        if ($this->shippingDate !== null) {
            $q['shippingDate'] = $this->shippingDate->format('d.m.Y');
            $q['shippingTime'] = $this->shippingDate->format('H:i');
        }
        $q['withPrice'] = $this->withPrice ? 'true' : 'false';
        $q['withExpectedDelivery'] = $this->withExpectedDelivery ? 'true' : 'false';
        $q['withGuiInformation'] = $this->withGuiInformation ? 'true' : 'false';
        if ($this->edi) {
            $q['edi'] = 'true';
        }
        if ($this->postingAtPostOffice) {
            $q['postingAtPostOffice'] = 'true';
        }
        if ($this->pid !== null) {
            $q['pid'] = $this->pid;
        }

        return $q;
    }
}
