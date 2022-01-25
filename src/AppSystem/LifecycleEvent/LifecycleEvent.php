<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\AppSystem\LifecycleEvent;

use BitBag\ShopwareAppSkeleton\AppSystem\Event\EventInterface;

final class LifecycleEvent implements LifecycleEventInterface
{
    private EventInterface $event;

    private string $eventType;

    public function __construct(EventInterface $event, string $eventType)
    {
        $this->event = $event;
        $this->eventType = $eventType;
    }

    public function getShopwareEvent(): EventInterface
    {
        return $this->event;
    }

    public function getEventType(): string
    {
        return $this->eventType;
    }
}
