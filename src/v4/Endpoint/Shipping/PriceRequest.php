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

    /** @return array<string, mixed> */
    public function toQuery(): array
    {
        $q = [
            'fromCountry' => $this->fromCountry->value,
            'fromPostalCode' => $this->fromPostalCode,
            'toCountry' => $this->toCountry->value,
            'toPostalCode' => $this->toPostalCode,
        ];

        foreach ($this->packages as $i => $pkg) {
            $q['weightInGrams'][$i] = (int) $pkg['weightInGrams'];
            if (isset($pkg['length'])) {
                $q['length'][$i] = (int) $pkg['length'];
            }
            if (isset($pkg['width'])) {
                $q['width'][$i] = (int) $pkg['width'];
            }
            if (isset($pkg['height'])) {
                $q['height'][$i] = (int) $pkg['height'];
            }
        }

        if ($this->products !== []) {
            $q['product'] = array_map(static fn (Product $p): string => $p->value, $this->products);
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
