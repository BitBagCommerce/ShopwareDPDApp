<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\AppSystem\Event;

interface EventInterface
{
    public function getShopUrl(): string;

    public function getShopId(): string;

    public function getAppVersion(): int;

    public function getEventData(): array;
}
