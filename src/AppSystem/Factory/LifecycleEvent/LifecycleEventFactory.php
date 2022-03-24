<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\AppSystem\Factory\LifecycleEvent;

use BitBag\ShopwareAppSkeleton\AppSystem\Client\ClientInterface;
use BitBag\ShopwareAppSkeleton\AppSystem\Event\EventInterface;
use BitBag\ShopwareAppSkeleton\AppSystem\LifecycleEvent\AppActivatedEvent;
use BitBag\ShopwareAppSkeleton\AppSystem\LifecycleEvent\AppDeactivatedEvent;
use BitBag\ShopwareAppSkeleton\AppSystem\LifecycleEvent\AppDeletedEvent;
use BitBag\ShopwareAppSkeleton\AppSystem\LifecycleEvent\AppInstalledEvent;
use BitBag\ShopwareAppSkeleton\AppSystem\LifecycleEvent\AppUpdatedEvent;
use BitBag\ShopwareAppSkeleton\AppSystem\LifecycleEvent\ClientAwareLifecycleEventInterface;
use BitBag\ShopwareAppSkeleton\AppSystem\LifecycleEvent\LifecycleEventInterface;

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

        throw new \InvalidArgumentException('Wrong event name: .' . $eventName);
    }

    public function createNew(string $eventName, EventInterface $event): LifecycleEventInterface
    {
        switch ($eventName) {
            case 'deactivated':
                return new AppDeactivatedEvent($event);
            case 'deleted':
                return new AppDeletedEvent($event);
        }

        throw new \InvalidArgumentException('Wrong event name: .' . $eventName);
    }
}
