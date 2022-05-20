<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\EventSubscriber;

use BitBag\ShopwareAppSystemBundle\LifecycleEvent\AppActivatedEvent;
use BitBag\ShopwareDpdApp\Plugin\CustomFieldSetConfiguratorInterface;
use BitBag\ShopwareDpdApp\Plugin\ShippingMethodConfiguratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AppActivatedEventSubscriber implements EventSubscriberInterface
{
    private CustomFieldSetConfiguratorInterface $customFieldSetConfigurator;

    private ShippingMethodConfiguratorInterface $shippingMethodConfigurator;

    public function __construct(
        CustomFieldSetConfiguratorInterface $customFieldSetConfigurator,
        ShippingMethodConfiguratorInterface $shippingMethodConfigurator
    ) {
        $this->customFieldSetConfigurator = $customFieldSetConfigurator;
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
        $this->customFieldSetConfigurator->createCustomFieldSetForPackageDetails($event->getContext());

        $this->shippingMethodConfigurator->createShippingMethod($event->getContext());
    }
}
