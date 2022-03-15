<?php

namespace BitBag\ShopwareAppSkeleton\EventSubscriber;

use BitBag\ShopwareAppSkeleton\AppSystem\Client\ClientInterface;
use BitBag\ShopwareAppSkeleton\AppSystem\LifecycleEvent\AppActivatedEvent;
use BitBag\ShopwareAppSkeleton\Entity\ShopInterface;
use DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AppActivatedEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            AppActivatedEvent::class => 'onAppActivated',
        ];
    }

    public function onAppActivated(AppActivatedEvent $event): void
    {
        $client = $event->getClient();
        $this->createShippingMethod($client);
        $this->createCustomFieldsForPackageDetailsInOrder($client);
    }

    private function createShippingMethod(ClientInterface $client): void
    {
        $shippingKey = ShopInterface::SHIPPING_KEY;
        $filterForShippingMethod = [
            'filter' => [
                [
                    'type' => 'contains',
                    'field' => 'name',
                    'value' => $shippingKey,
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
            'name' => $shippingKey,
            'active' => true,
            'description' => $shippingKey.' shipping method',
            'taxType' => 'auto',
            'translated' => [
                'name' => $shippingKey,
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

    private function createCustomFieldsForPackageDetailsInOrder(ClientInterface $client): void
    {
        $customFieldSetFilter = [
            'filter' => [
                [
                    'type' => 'equals',
                    'field' => 'name',
                    'value' => 'package_details',
                ],
            ],
        ];

        $customFieldNames = [
            [
                'name' => 'height',
                'label' => 'Height',
                'type' => 'int',
            ],
            [
                'name' => 'width',
                'label' => 'Width',
                'type' => 'int',
            ],
            [
                'name' => 'depth',
                'label' => 'Depth',
                'type' => 'int',
            ],
            [
                'name' => 'description',
                'label' => 'Description',
                'type' => 'text',
            ],
            [
                'name' => 'countryCode',
                'label' => 'Sender country code',
                'type' => 'text',
            ],
        ];

        $customFieldPrefix = 'package_details';

        foreach ($customFieldNames as $key => $item) {
            $customFieldSetId = null;
            $type = $item['type'];

            $customFieldName = $customFieldPrefix.'_'.$item['name'];

            $customFieldFilter = [
                'filter' => [
                    [
                        'type' => 'equals',
                        'field' => 'name',
                        'value' => $customFieldName,
                    ],
                ],
            ];

            $customField = $client->searchIds('custom-field', $customFieldFilter);
            if (!$customField || 0 === $customField['total']) {
                $customFieldSet = $client->search('custom-field-set', $customFieldSetFilter);
                if (!$customFieldSet || 0 === $customFieldSet['total']) {
                    $customFieldSet = [
                        'name' => $customFieldPrefix,
                        'relations' => [
                            [
                                'entityName' => 'order',
                            ],
                        ],
                        'config' => [
                            'label' => ['en-GB' => 'Package details'],
                            'translated' => true,
                        ],
                    ];
                } else {
                    $customFieldSetId = $customFieldSet['data'][0]['id'];
                }

                $customFieldArr = [
                    'name' => $customFieldName,
                    'type' => $type,
                    'position' => $key,
                    'config' => [
                        'type' => $item['type'],
                        'label' => ['en-GB' => $item['label']],
                        'helpText' => [],
                        'placeholder' => [],
                        'componentName' => 'sw-field',
                        'customFieldType' => $item['type'],
                        'customFieldPosition' => $key,
                    ],
                ];

                if ($customFieldSetId) {
                    $customFieldArr['customFieldSetId'] = $customFieldSetId;
                } else {
                    $customFieldArr['customFieldSet'] = $customFieldSet;
                }

                if ('int' === $type) {
                    $customFieldArr['config']['type'] = 'number';
                    $customFieldArr['config']['numberType'] = $type;
                    $customFieldArr['config']['customFieldType'] = 'number';
                }

                $client->createEntity('custom-field', $customFieldArr);
            }
        }
    }
}
