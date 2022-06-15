<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\EventSubscriber;

use BitBag\ShopwareAppSystemBundle\AppLifecycleEvent\AppActivatedEvent;
use BitBag\ShopwareDpdApp\Plugin\ShippingMethodConfiguratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AppActivatedEventSubscriber implements EventSubscriberInterface
{
    private ShippingMethodConfiguratorInterface $shippingMethodConfigurator;

    public function __construct(
        ShippingMethodConfiguratorInterface $shippingMethodConfigurator
    ) {
        $this->shippingMethodConfigurator = $shippingMethodConfigurator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AppActivatedEvent::class => 'onAppActivated',
        ];
    }

    public function onAppActivated(AppActivatedEvent $event): void
    {
        $this->shippingMethodConfigurator->createShippingMethod($event->getContext());
    }
}
