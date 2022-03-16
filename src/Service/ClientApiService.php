<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Service;

use BitBag\ShopwareDpdApp\AppSystem\Client\ClientInterface;

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
}
