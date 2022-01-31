<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\AppSystem\EventSubscriber;

use BitBag\ShopwareAppSkeleton\AppSystem\LifecycleEvent\AppDeletedEvent;
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
            AppDeletedEvent::class => 'onAppDeleted',
        ];
    }

    public function onAppDeleted(AppDeletedEvent $event): void
    {
        $shopId = $event->getShopwareEvent()->getShopId();
        $shop = $this->shopRepository->getOneByShopId($shopId);

        $this->entityManager->remove($shop);
        $this->entityManager->flush();
    }
}
