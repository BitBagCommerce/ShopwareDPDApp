<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\EventSubscriber;

use BitBag\ShopwareAppSystemBundle\LifecycleEvent\AppActivatedEvent;
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
    }
}
