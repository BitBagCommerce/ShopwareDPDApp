<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\AppSystem\LifecycleEvent;

use BitBag\ShopwareDpdApp\AppSystem\Client\ClientInterface;

interface ClientAwareLifecycleEventInterface extends LifecycleEventInterface
{
    public function getClient(): ClientInterface;
}
