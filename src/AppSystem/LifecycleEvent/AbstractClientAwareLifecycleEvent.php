<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\AppSystem\LifecycleEvent;

use BitBag\ShopwareDpdApp\AppSystem\Client\ClientInterface;
use BitBag\ShopwareDpdApp\AppSystem\Event\EventInterface;

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
