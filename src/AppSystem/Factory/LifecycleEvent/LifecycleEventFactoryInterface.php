<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\AppSystem\Factory\LifecycleEvent;

use BitBag\ShopwareDpdApp\AppSystem\Client\ClientInterface;
use BitBag\ShopwareDpdApp\AppSystem\Event\EventInterface;
use BitBag\ShopwareDpdApp\AppSystem\LifecycleEvent\ClientAwareLifecycleEventInterface;
use BitBag\ShopwareDpdApp\AppSystem\LifecycleEvent\LifecycleEventInterface;

interface LifecycleEventFactoryInterface
{
    public function createWithClient(string $eventName, EventInterface $event, ClientInterface $client): ClientAwareLifecycleEventInterface;

    public function createNew(string $eventName, EventInterface $event): LifecycleEventInterface;
}
