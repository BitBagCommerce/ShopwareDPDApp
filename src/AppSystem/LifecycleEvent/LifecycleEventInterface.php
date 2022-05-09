<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\AppSystem\LifecycleEvent;

use BitBag\ShopwareDpdApp\AppSystem\Event\EventInterface;

interface LifecycleEventInterface
{
    public function getShopwareEvent(): EventInterface;
}
