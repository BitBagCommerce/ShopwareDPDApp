<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Service;

use BitBag\ShopwareDpdApp\AppSystem\Client\ClientInterface;
use BitBag\ShopwareDpdApp\Factory\ShippingMethodFactoryInterface;

final class ClientApiService
{
    public function getOrder(ClientInterface $client, string $orderId): array
    {
        $orderAddressFilter = [
            'filter' => [
                [
                    'type' => 'equals',
                    'field' => 'id',
                    'value' => $orderId,
                ],
            ],
            'associations' => [
                'lineItems' => [
                    'associations' => [
                        'product' => [],
                    ],
                ],
                'deliveries' => [
                    'associations' => [
                        'shippingMethod' => [],
                    ],
                ],
            ],
        ];

        return $client->search('order', $orderAddressFilter)['data'][0];
    }

    public function findDeliveryTimeByMinMax(int $min, int $max, ClientInterface $client): array
    {
        $filterForDeliveryTime = [
            'filter' => [
                [
                    'type' => 'contains',
                    'field' => 'unit',
                    'value' => 'day',
                ],
                [
                    'type' => 'equals',
                    'field' => 'min',
                    'value' => $min,
                ],
                [
                    'type' => 'equals',
                    'field' => 'max',
                    'value' => $max,
                ],
            ],
        ];

        return $client->searchIds('delivery-time', $filterForDeliveryTime);
    }

    public function findShippingMethodByShippingKey(ClientInterface $client): array
    {
        $shippingKey = ShippingMethodFactoryInterface::SHIPPING_KEY;
        $filterForShippingMethod = [
            'filter' => [
                [
                    'type' => 'contains',
                    'field' => 'name',
                    'value' => $shippingKey,
                ],
            ],
        ];

        return $client->searchIds('shipping-method', $filterForShippingMethod);
    }

    public function findRuleByName(string $name, ClientInterface $client): array
    {
        $filterRule = [
            'filter' => [
                [
                    'type' => 'equals',
                    'field' => 'name',
                    'value' => $name,
                ],
            ],
        ];

        return $client->searchIds('rule', $filterRule);
    }

    public function findRandomRule(ClientInterface $client): array
    {
        return $client->searchIds('rule', []);
    }

    public function findIdsCustomFieldByName(string $name, ClientInterface $client): array
    {
        $customFieldFilter = [
            'filter' => [
                [
                    'type' => 'equals',
                    'field' => 'name',
                    'value' => $name,
                ],
            ],
        ];

        return $client->searchIds('custom-field', $customFieldFilter);
    }

    public function findCustomFieldSetByName(string $name, ClientInterface $client): array
    {
        $customFieldFilter = [
            'filter' => [
                [
                    'type' => 'equals',
                    'field' => 'name',
                    'value' => $name,
                ],
            ],
        ];

        return $client->search('custom-field-set', $customFieldFilter);
    }
}
