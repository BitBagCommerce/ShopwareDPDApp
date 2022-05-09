<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\AppSystem\LifecycleEvent;

use BitBag\ShopwareDpdApp\AppSystem\Event\EventInterface;

abstract class AbstractLifecycleEvent implements LifecycleEventInterface
{
    private EventInterface $event;

    public function __construct(EventInterface $event)
    {
        $this->event = $event;
    }

    public function getShopwareEvent(): EventInterface
    {
        return $this->event;
    }
}
