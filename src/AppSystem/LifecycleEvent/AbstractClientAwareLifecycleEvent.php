<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\AppSystem\LifecycleEvent;

use BitBag\ShopwareAppSkeleton\AppSystem\Client\ClientInterface;
use BitBag\ShopwareAppSkeleton\AppSystem\Event\EventInterface;

abstract class AbstractClientAwareLifecycleEvent extends AbstractLifecycleEvent implements ClientAwareLifecycleEventInterface
{
    private ClientInterface $client;

    public function __construct(EventInterface $event, ClientInterface $client)
    {
        $this->client = $client;

        parent::__construct($event);
    }

    public function getClient(): ClientInterface
    {
        return $this->client;
    }
}
