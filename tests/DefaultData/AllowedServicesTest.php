<?php

namespace Crakter\BringApi\DefaultData;

use PHPUnit\Framework\TestCase;
use Crakter\BringApi\Exception\ProductAppliesToNotAllowedException;

class AllowedServicesTest extends TestCase
{
    /**
     * @dataProvider additionProviderHasAppliesToAllowed
     */
    public function testHasAppliesToAllowedValue(string $allowed, array $onProducts)
    {
        foreach($onProducts as $product) {
            $this->assertTrue(AllowedServices::hasAppliesTo($allowed, $product));
        }
    }

    /**
     * @dataProvider additionProviderHasAppliesToNotAllowed
     */
    public function testHasAppliesToNotAllowedValue(string $notAllowed, array $onProducts)
    {
        $this->expectException(ProductAppliesToNotAllowedException::class);
        foreach($onProducts as $product) {
            AllowedServices::hasAppliesTo($notAllowed, $product);
        }
    }

    public function additionProviderHasAppliesToNotAllowed()
    {
        return [
            ['cashOnDelivery', [
                'SERVICEPAKKE',
                'PA_DOREN',
                'EKSPRESS09',
                'BUSINESS_PALLET',
                'BUSINESS_PARCEL_HALFPALLET',
                'BUSINESS_PARCEL_QUARTERPALLET',
                'EXPRESS_NORDIC_0900',
            ]],
            ['socialControl', [
                'BPAKKE_DOR-DOR',
                'PICKUP_PARCEL',
                'PICKUP_PARCEL_BULK',
                'HOME_DELIVERY_PARCEL',
                'BUSINESS_PARCEL',
                'BUSINESS_PARCEL_BULK',
                'EXPRESS_NORDIC_0900_BULK',
                'PA_DOREN',
                'EKSPRESS09',
                'BUSINESS_PALLET',
                'BUSINESS_PARCEL_HALFPALLET',
                'BUSINESS_PARCEL_QUARTERPALLET',
                'EXPRESS_NORDIC_0900',
            ]],
            ['simpleDelivery', [
                'PICKUP_PARCEL',
                'PICKUP_PARCEL_BULK',
                'HOME_DELIVERY_PARCEL',
                'BUSINESS_PARCEL',
                'BUSINESS_PARCEL_BULK',
                'EXPRESS_NORDIC_0900_BULK',
                'SERVICEPAKKE',
                'EKSPRESS09',
                'BUSINESS_PALLET',
                'BUSINESS_PARCEL_HALFPALLET',
                'BUSINESS_PARCEL_QUARTERPALLET',
                'EXPRESS_NORDIC_0900',
            ]],
            ['deliveryOption', [
                'HOME_DELIVERY_PARCEL',
                'BUSINESS_PARCEL',
                'BUSINESS_PARCEL_BULK',
                'EXPRESS_NORDIC_0900_BULK',
                'SERVICEPAKKE',
                'PA_DOREN',
                'EKSPRESS09',
                'BUSINESS_PALLET',
                'BUSINESS_PARCEL_HALFPALLET',
                'BUSINESS_PARCEL_QUARTERPALLET',
                'EXPRESS_NORDIC_0900',
            ]],
            ['saturdayDelivery', [
                'BPAKKE_DOR-DOR',
                'PICKUP_PARCEL',
                'PICKUP_PARCEL_BULK',
                'HOME_DELIVERY_PARCEL',
                'BUSINESS_PARCEL',
                'BUSINESS_PARCEL_BULK',
                'EXPRESS_NORDIC_0900_BULK',
                'SERVICEPAKKE',
                'PA_DOREN',
                'BUSINESS_PALLET',
                'BUSINESS_PARCEL_HALFPALLET',
                'BUSINESS_PARCEL_QUARTERPALLET',
                'EXPRESS_NORDIC_0900',
            ]],
            ['flexDelivery', [
                'BPAKKE_DOR-DOR',
                'SERVICEPAKKE',
                'PA_DOREN',
                'EKSPRESS09',
            ]],
            ['phonenotification', [
                'BPAKKE_DOR-DOR',
                'SERVICEPAKKE',
                'PA_DOREN',
                'EKSPRESS09',
                'PICKUP_PARCEL',
                'PICKUP_PARCEL_BULK',
                'HOME_DELIVERY_PARCEL',
            ]],
            ['deliveryIndoors', [
                'BPAKKE_DOR-DOR',
                'SERVICEPAKKE',
                'PA_DOREN',
                'EKSPRESS09',
                'PICKUP_PARCEL',
                'PICKUP_PARCEL_BULK',
                'HOME_DELIVERY_PARCEL',
            ]],
        ];
    }

    public function additionProviderHasAppliesToAllowed()
    {
        return [
            ['cashOnDelivery', [
                'BPAKKE_DOR-DOR',
                'PICKUP_PARCEL',
                'PICKUP_PARCEL_BULK',
                'HOME_DELIVERY_PARCEL',
                'BUSINESS_PARCEL',
                'BUSINESS_PARCEL_BULK',
                'EXPRESS_NORDIC_0900_BULK',
            ]],
            ['recipientNotification', [
                'SERVICEPAKKE',
                'BPAKKE_DOR-DOR',
                'PA_DOREN',
                'EKSPRESS09',
                'PICKUP_PARCEL',
                'PICKUP_PARCEL_BULK',
                'HOME_DELIVERY_PARCEL',
                'BUSINESS_PARCEL',
                'BUSINESS_PARCEL_BULK',
                'EXPRESS_NORDIC_0900_BULK',
                'BUSINESS_PALLET',
                'BUSINESS_PARCEL_HALFPALLET',
                'BUSINESS_PARCEL_QUARTERPALLET',
                'EXPRESS_NORDIC_0900',
            ]],
            ['socialControl', [
                'SERVICEPAKKE',
            ]],
            ['simpleDelivery', [
                'BPAKKE_DOR-DOR',
                'PA_DOREN',
            ]],
            ['deliveryOption', [
                'BPAKKE_DOR-DOR',
                'PICKUP_PARCEL',
                'PICKUP_PARCEL_BULK',
            ]],
            ['saturdayDelivery', [
                'EKSPRESS09',
            ]],
            ['flexDelivery', [
                'PICKUP_PARCEL',
                'PICKUP_PARCEL_BULK',
                'HOME_DELIVERY_PARCEL',
                'BUSINESS_PARCEL',
                'BUSINESS_PARCEL_BULK',
                'EXPRESS_NORDIC_0900_BULK',
                'BUSINESS_PALLET',
                'BUSINESS_PARCEL_HALFPALLET',
                'BUSINESS_PARCEL_QUARTERPALLET',
                'EXPRESS_NORDIC_0900',
            ]],
            ['phonenotification', [
                'BUSINESS_PARCEL',
                'BUSINESS_PARCEL_BULK',
                'EXPRESS_NORDIC_0900_BULK',
                'BUSINESS_PALLET',
                'BUSINESS_PARCEL_HALFPALLET',
                'BUSINESS_PARCEL_QUARTERPALLET',
                'EXPRESS_NORDIC_0900',
            ]],
            ['deliveryIndoors', [
                'BUSINESS_PARCEL',
                'BUSINESS_PARCEL_BULK',
                'EXPRESS_NORDIC_0900_BULK',
                'BUSINESS_PALLET',
                'BUSINESS_PARCEL_HALFPALLET',
                'BUSINESS_PARCEL_QUARTERPALLET',
                'EXPRESS_NORDIC_0900',
            ]],
        ];
    }
}
