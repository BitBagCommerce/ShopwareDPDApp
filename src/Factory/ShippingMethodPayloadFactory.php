<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use DateTime;
use Vin\ShopwareSdk\Repository\Struct\IdSearchResult;

final class ShippingMethodPayloadFactory implements ShippingMethodPayloadFactoryInterface
{
    public function create(
        string $ruleId,
        string $currencyId,
        IdSearchResult $deliveryTime
    ): array {
        $name = self::SHIPPING_KEY;

        $currentDateTime = new DateTime();

        $dpdShippingMethod = [
            'name' => $name,
            'active' => true,
            'description' => $name . ' shipping method',
            'taxType' => 'auto',
            'translated' => [
                'name' => $name,
            ],
            'customFields' => [
                'technical_name' => $name,
            ],
            'availabilityRuleId' => $ruleId,
            'trackingUrl' => 'https://tracktrace.dpd.com.pl/parcelDetails?typ=1&p1=%s',
            'prices' => [
                [
                    'ruleId' => $ruleId,
                    'calculation' => 1,
                    'quantityStart' => 1,
                    'currencyPrice' => [
                        $currencyId => [
                            'net' => 0.0,
                            'gross' => 0.0,
                            'linked' => false,
                            'currencyId' => $currencyId,
                        ],
                    ],
                ],
            ],
        ];

        if (0 < $deliveryTime->getTotal()) {
            $dpdShippingMethod = array_merge($dpdShippingMethod, [
                'deliveryTimeId' => $deliveryTime->firstId(),
            ]);
        } else {
            $dpdShippingMethod = array_merge($dpdShippingMethod, [
                'deliveryTime' => [
                    'name' => '1-3 days',
                    'min' => 1,
                    'max' => 3,
                    'unit' => 'day',
                    'createdAt' => $currentDateTime,
                ],
            ]);
        }

        return $dpdShippingMethod;
    }
}
