<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use BitBag\ShopwareDpdApp\AppSystem\Client\ClientInterface;
use BitBag\ShopwareDpdApp\Entity\ShopInterface;
use DateTime;

final class CreateShippingMethodFactory implements CreateShippingMethodFactoryInterface
{
    public function create(string $ruleId, array $deliveryTime, ClientInterface $client): void
    {
        $shippingKey = ShopInterface::SHIPPING_KEY;
        $currentDateTime = new DateTime('now');

        $dpdShippingMethod = [
            'name' => $shippingKey,
            'active' => true,
            'description' => $shippingKey.' shipping method',
            'taxType' => 'auto',
            'translated' => [
                'name' => $shippingKey,
            ],
            'availabilityRuleId' => $ruleId,
            'createdAt' => $currentDateTime,
        ];

        if (isset($deliveryTime['total']) && $deliveryTime['total'] > 0) {
            $dpdShippingMethod = array_merge($dpdShippingMethod, [
                'deliveryTimeId' => $deliveryTime['data'][0],
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

        $client->createEntity('shipping-method', $dpdShippingMethod);
    }
}
