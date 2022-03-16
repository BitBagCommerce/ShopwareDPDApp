<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\AppSystem;

use BitBag\ShopwareDpdApp\AppSystem\Event\EventInterface;

interface AppLifecycleHandlerInterface
{
    public function appInstalled(EventInterface $event): void;

    public function appUpdated(EventInterface $event): void;

    public function appActivated(EventInterface $event): void;

    public function appDeactivated(EventInterface $event): void;

    public function appDeleted(EventInterface $event): void;
}
