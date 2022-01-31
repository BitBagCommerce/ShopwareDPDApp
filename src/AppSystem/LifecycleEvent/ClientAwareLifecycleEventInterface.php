<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\AppSystem\LifecycleEvent;

use BitBag\ShopwareAppSkeleton\AppSystem\Client\ClientInterface;

interface ClientAwareLifecycleEventInterface extends LifecycleEventInterface
{
    public function getClient(): ClientInterface;
}
