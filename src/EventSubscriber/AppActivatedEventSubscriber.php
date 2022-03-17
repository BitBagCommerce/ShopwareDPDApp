<?php

namespace BitBag\ShopwareDpdApp\EventSubscriber;

use BitBag\ShopwareDpdApp\AppSystem\Client\ClientInterface;
use BitBag\ShopwareDpdApp\AppSystem\LifecycleEvent\AppActivatedEvent;
use BitBag\ShopwareDpdApp\Factory\CreateCustomFieldFactoryInterface;
use BitBag\ShopwareDpdApp\Factory\CreateShippingMethodFactoryInterface;
use BitBag\ShopwareDpdApp\Service\ClientApiService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AppActivatedEventSubscriber implements EventSubscriberInterface
{
    private CreateShippingMethodFactoryInterface $createShippingMethodFactory;

    private ClientApiService $clientApiService;

    private CreateCustomFieldFactoryInterface $createCustomFieldFactory;

    public function __construct(
        CreateShippingMethodFactoryInterface $createShippingMethodFactory,
        ClientApiService $clientApiService,
        CreateCustomFieldFactoryInterface $createCustomFieldFactory
    ) {
        $this->createShippingMethodFactory = $createShippingMethodFactory;
        $this->clientApiService = $clientApiService;
        $this->createCustomFieldFactory = $createCustomFieldFactory;
    }

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
        $shippingMethods = $this->clientApiService->findShippingMethodByShippingKey($client);
        if ($shippingMethods['total']) {
            return;
        }

        $deliveryTime = $this->clientApiService->findDeliveryTimeByMinMax(1, 3, $client);

        $rule = $this->clientApiService->findRuleByName('Cart >= 0', $client);
        if (!$rule) {
            $rule = $this->clientApiService->findRandomRule($client);
        }

        $this->createShippingMethodFactory->create($rule['data'][0], $deliveryTime, $client);
    }

    private function createCustomFieldsForPackageDetailsInOrder(ClientInterface $client): void
    {
        $customFieldPrefix = 'package_details';

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

        foreach ($customFieldNames as $key => $item) {
            $customFieldSetId = null;
            $type = $item['type'];

            $customFieldName = $customFieldPrefix.'_'.$item['name'];

            $customField = $this->clientApiService->findIdsCustomFieldByName($customFieldName, $client);
            if (0 === $customField['total']) {
                $customFieldSet = $this->clientApiService->findCustomFieldSetByName($customFieldPrefix, $client);
                if (0 === $customFieldSet['total']) {
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

                $this->createCustomFieldFactory->create(
                    $customFieldName,
                    $type,
                    $key,
                    $item['label'],
                    $client,
                    $customFieldSetId,
                    $customFieldSet
                );
            }
        }
    }
}
