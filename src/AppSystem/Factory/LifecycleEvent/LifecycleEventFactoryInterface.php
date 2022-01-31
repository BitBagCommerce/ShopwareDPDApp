<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\AppSystem\Factory\LifecycleEvent;

use BitBag\ShopwareAppSkeleton\AppSystem\Client\ClientInterface;
use BitBag\ShopwareAppSkeleton\AppSystem\Event\EventInterface;
use BitBag\ShopwareAppSkeleton\AppSystem\LifecycleEvent\ClientAwareLifecycleEventInterface;
use BitBag\ShopwareAppSkeleton\AppSystem\LifecycleEvent\LifecycleEventInterface;

interface LifecycleEventFactoryInterface
{
    public function createWithClient(string $eventName, EventInterface $event, ClientInterface $client): ClientAwareLifecycleEventInterface;

    public function createNew(string $eventName, EventInterface $event): LifecycleEventInterface;
}
