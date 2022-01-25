<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\AppSystem\LifecycleEvent;

use BitBag\ShopwareAppSkeleton\AppSystem\Event\EventInterface;

interface LifecycleEventInterface
{
    public const APP_INSTALLED = 'installed';

    public const APP_UPDATED = 'updated';

    public const APP_ACTIVATED = 'activated';

    public const APP_DEACTIVATED = 'deactivated';

    public const APP_DELETED = 'deleted';

    public function getShopwareEvent(): EventInterface;

    public function getEventType(): string;
}
