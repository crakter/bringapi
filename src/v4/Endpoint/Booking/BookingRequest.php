<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Booking;

use Bring\Api\Dto\Address;
use Bring\Api\Dto\Package;
use Bring\Api\Enum\AdditionalService;
use Bring\Api\Enum\Product;
use Bring\Api\Exception\InvalidArgumentException;

/**
 * Bring Booking API request body. One BookingRequest produces one or more
 * consignments in the same POST.
 */
final class BookingRequest
{
    /**
     * @param string                  $schemaVersion  current Bring Booking API schema version (currently "1")
     * @param string                  $customerNumber Mybring customer number, e.g. "PARCELS_NORWAY-10001123123"
     * @param list<Consignment>       $consignments
     * @param bool                    $testIndicator  ALSO sets the X-Bring-Test-Indicator header when sent
     */
    public function __construct(
        public readonly string $schemaVersion,
        public readonly string $customerNumber,
        public readonly array $consignments,
        public readonly bool $testIndicator = false,
    ) {
        if ($consignments === []) {
            throw new InvalidArgumentException('BookingRequest: at least one consignment is required.');
        }
        if ($customerNumber === '') {
            throw new InvalidArgumentException('BookingRequest: customerNumber must not be empty.');
        }
    }

    public function asTest(): self
    {
        return new self($this->schemaVersion, $this->customerNumber, $this->consignments, true);
    }

    /**
     * Convenience for the most common single-consignment booking.
     *
     * @param list<Package>           $packages
     * @param list<AdditionalService> $additional
     */
    public static function single(
        string $schemaVersion,
        string $customerNumber,
        Product $product,
        Address $sender,
        Address $recipient,
        array $packages,
        array $additional = [],
        bool $testIndicator = false,
    ): self {
        $consignment = new Consignment(
            product: new BookingProduct($product, $additional),
            sender: $sender,
            recipient: $recipient,
            packages: $packages,
        );

        return new self($schemaVersion, $customerNumber, [$consignment], $testIndicator);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        // Bring's Booking API expects customerNumber inside each
        // consignment's product object — not at the request root. Posting
        // it at the root produces a per-consignment BOOK-INPUT-019
        // ("Customer number must be provided") even though the SDK
        // constructor enforces a non-empty value, because Bring reads it
        // from the product slot and finds nothing there.
        //
        // We model customerNumber once on the request (it's per-request in
        // practice) and inject it into each consignment's product at
        // serialization time, so the public SDK shape stays unchanged.
        $customerNumber = $this->customerNumber;
        $consignments = array_map(
            static function (Consignment $c) use ($customerNumber): array {
                $arr = $c->toArray();
                $arr['product'] = ['customerNumber' => $customerNumber] + (array) $arr['product'];

                return $arr;
            },
            $this->consignments,
        );

        return [
            'schemaVersion' => $this->schemaVersion,
            'consignments' => $consignments,
            'testIndicator' => $this->testIndicator,
        ];
    }
}
