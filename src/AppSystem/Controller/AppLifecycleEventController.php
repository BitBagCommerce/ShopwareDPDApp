<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\AppSystem\Controller;

use BitBag\ShopwareAppSkeleton\AppSystem\Client\ClientInterface;
use BitBag\ShopwareAppSkeleton\AppSystem\Event\EventInterface;
use BitBag\ShopwareAppSkeleton\AppSystem\Factory\LifecycleEvent\LifecycleEventFactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

final class AppLifecycleEventController
{
    private EventDispatcherInterface $eventDispatcher;

    private LifecycleEventFactoryInterface $eventFactory;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        LifecycleEventFactoryInterface $eventFactory
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->eventFactory = $eventFactory;
    }

    public function onEvent(EventInterface $event, string $eventType): Response
    {
        $event = $this->eventFactory->createNew($eventType, $event);

        $this->eventDispatcher->dispatch($event);

        return new Response();
    }

    public function onEventWithClient(EventInterface $event, ClientInterface $client, string $eventType): Response
    {
        $event = $this->eventFactory->createWithClient($eventType, $event, $client);

        $this->eventDispatcher->dispatch($event);

        return new Response();
    }
}
