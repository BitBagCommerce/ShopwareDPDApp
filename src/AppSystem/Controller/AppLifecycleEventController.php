<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\AppSystem\Controller;

use BitBag\ShopwareAppSkeleton\AppSystem\Event\EventInterface;
use BitBag\ShopwareAppSkeleton\AppSystem\LifecycleEvent\LifecycleEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

final class AppLifecycleEventController
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(EventInterface $event, string $eventType): Response
    {
        $this->eventDispatcher->dispatch(new LifecycleEvent($event, $eventType));

        return new Response();
    }
}
