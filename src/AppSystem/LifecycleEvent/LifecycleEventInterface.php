<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\AppSystem\LifecycleEvent;

use BitBag\ShopwareAppSkeleton\AppSystem\Event\EventInterface;

interface LifecycleEventInterface
{
    public function getShopwareEvent(): EventInterface;
}
