<?php

namespace BitBag\ShopwareAppSkeleton\EventSubscriber;

use BitBag\ShopwareAppSkeleton\AppSystem\LifecycleEvent\AppActivatedEvent;
use DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AppActivatedEventSubscriber implements EventSubscriberInterface
{
    public const SHIPPING_KEY = 'DPD';

    public static function getSubscribedEvents(): array
    {
        return [
            AppActivatedEvent::class => 'onAppActivated',
        ];
    }

    public function onAppActivated(AppActivatedEvent $event): void
    {
        $client = $event->getClient();

        $filterForShippingMethod = [
            'filter' => [
                [
                    'type' => 'contains',
                    'field' => 'name',
                    'value' => self::SHIPPING_KEY,
                ],
            ],
        ];

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
                    'value' => 1,
                ],
                [
                    'type' => 'equals',
                    'field' => 'max',
                    'value' => 3,
                ],
            ],
        ];

        $shippingMethods = $client->searchIds('shipping-method', $filterForShippingMethod);
        if ($shippingMethods['total']) {
            return;
        }

        $deliveryTime = $client->searchIds('delivery-time', $filterForDeliveryTime);

        $filterRule = [
            'filter' => [
                [
                    'type' => 'equals',
                    'field' => 'name',
                    'value' => 'Cart >= 0',
                ],
            ],
        ];

        $rule = $client->searchIds('rule', $filterRule);
        if (!$rule) {
            $rule = $client->searchIds('rule', []);
        }

        $currentDateTime = new DateTime('now');

        $dpdShippingMethod = [
            'name' => self::SHIPPING_KEY,
            'active' => true,
            'description' => self::SHIPPING_KEY . " shipping method",
            'taxType' => 'auto',
            'translated' => [
                'name' => self::SHIPPING_KEY,
            ],
            'availabilityRuleId' => $rule['data'][0],
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
