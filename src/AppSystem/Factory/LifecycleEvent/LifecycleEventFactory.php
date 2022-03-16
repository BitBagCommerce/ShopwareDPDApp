<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\AppSystem\Factory\LifecycleEvent;

use BitBag\ShopwareDpdApp\AppSystem\Client\ClientInterface;
use BitBag\ShopwareDpdApp\AppSystem\Event\EventInterface;
use BitBag\ShopwareDpdApp\AppSystem\LifecycleEvent\AppActivatedEvent;
use BitBag\ShopwareDpdApp\AppSystem\LifecycleEvent\AppDeactivatedEvent;
use BitBag\ShopwareDpdApp\AppSystem\LifecycleEvent\AppDeletedEvent;
use BitBag\ShopwareDpdApp\AppSystem\LifecycleEvent\AppInstalledEvent;
use BitBag\ShopwareDpdApp\AppSystem\LifecycleEvent\AppUpdatedEvent;
use BitBag\ShopwareDpdApp\AppSystem\LifecycleEvent\ClientAwareLifecycleEventInterface;
use BitBag\ShopwareDpdApp\AppSystem\LifecycleEvent\LifecycleEventInterface;

final class LifecycleEventFactory implements LifecycleEventFactoryInterface
{
    public function createWithClient(string $eventName, EventInterface $event, ClientInterface $client): ClientAwareLifecycleEventInterface
    {
        switch ($eventName) {
            case 'activated':
                return new AppActivatedEvent($event, $client);
            case 'installed':
                return new AppInstalledEvent($event, $client);
            case 'updated':
                return new AppUpdatedEvent($event, $client);
        }

        throw new \InvalidArgumentException('Wrong event name: .'.$eventName);
    }

    public function createNew(string $eventName, EventInterface $event): LifecycleEventInterface
    {
        switch ($eventName) {
            case 'deactivated':
                return new AppDeactivatedEvent($event);
            case 'deleted':
                return new AppDeletedEvent($event);
        }

        throw new \InvalidArgumentException('Wrong event name: .'.$eventName);
    }
}
