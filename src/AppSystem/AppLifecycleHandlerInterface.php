<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\AppSystem;

use BitBag\ShopwareAppSkeleton\AppSystem\Event\EventInterface;

interface AppLifecycleHandlerInterface
{
    public function appInstalled(EventInterface $event): void;

    public function appUpdated(EventInterface $event): void;

    public function appActivated(EventInterface $event): void;

    public function appDeactivated(EventInterface $event): void;

    public function appDeleted(EventInterface $event): void;
}
