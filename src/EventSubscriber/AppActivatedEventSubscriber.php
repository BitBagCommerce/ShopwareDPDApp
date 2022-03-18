<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\EventSubscriber;

use BitBag\ShopwareDpdApp\AppSystem\Client\ClientInterface;
use BitBag\ShopwareDpdApp\AppSystem\LifecycleEvent\AppActivatedEvent;
use BitBag\ShopwareDpdApp\Factory\CreateDetailsPackageFieldsFactoryInterface;
use BitBag\ShopwareDpdApp\Factory\CreateShippingMethodFactoryInterface;
use BitBag\ShopwareDpdApp\Service\ClientApiService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AppActivatedEventSubscriber implements EventSubscriberInterface
{
    private CreateShippingMethodFactoryInterface $createShippingMethodFactory;

    private ClientApiService $clientApiService;

    private CreateDetailsPackageFieldsFactoryInterface $createDetailsPackageFieldsFactory;

    public function __construct(
        CreateShippingMethodFactoryInterface $createShippingMethodFactory,
        ClientApiService $clientApiService,
        CreateDetailsPackageFieldsFactoryInterface $createDetailsPackageFieldsFactory
    ) {
        $this->createShippingMethodFactory = $createShippingMethodFactory;
        $this->clientApiService = $clientApiService;
        $this->createDetailsPackageFieldsFactory = $createDetailsPackageFieldsFactory;
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
        $this->createDetailsPackageFieldsFactory->create($client);
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
}
