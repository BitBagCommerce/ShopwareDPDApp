<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\AppSystem\EventSubscriber;

use BitBag\ShopwareAppSkeleton\AppSystem\LifecycleEvent\LifecycleEvent;
use BitBag\ShopwareAppSkeleton\AppSystem\LifecycleEvent\LifecycleEventInterface;
use BitBag\ShopwareAppSkeleton\Repository\ShopRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class LifecycleEventSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $entityManager;

    private ShopRepositoryInterface $shopRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ShopRepositoryInterface $shopRepository
    ) {
        $this->entityManager = $entityManager;
        $this->shopRepository = $shopRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LifecycleEvent::class => 'onLifecycleEvent',
        ];
    }

    public function onLifecycleEvent(LifecycleEventInterface $event): void
    {
        if (LifecycleEventInterface::APP_DELETED !== $event->getEventType()) {
            return;
        }

        $shopId = $event->getShopwareEvent()->getShopId();
        $shop = $this->shopRepository->getOneByShopId($shopId);

        $this->entityManager->remove($shop);
        $this->entityManager->flush();
    }
}
