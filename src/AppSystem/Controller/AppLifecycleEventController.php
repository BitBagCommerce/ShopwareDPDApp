<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\AppSystem\Controller;

use BitBag\ShopwareAppSkeleton\AppSystem\AppLifecycleHandlerInterface;
use BitBag\ShopwareAppSkeleton\AppSystem\Event\EventInterface;
use BitBag\ShopwareAppSkeleton\AppSystem\Exception\ShopNotFoundException;
use BitBag\ShopwareAppSkeleton\Repository\ShopRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppLifecycleEventController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    private ShopRepositoryInterface $shopRepository;

    private iterable $handlers;

    public function __construct(
        iterable $handlers,
        EntityManagerInterface $entityManager,
        ShopRepositoryInterface $shopRepository
    ) {
        $this->handlers = $handlers;
        $this->entityManager = $entityManager;
        $this->shopRepository = $shopRepository;
    }

    /**
     * @Route("/applifecycle/installed", name="applifecycle.installed", methods={"POST"})
     * The event `app.installed` gets triggered each time your app gets installed.
     * At this point the shop is already registered.
     */
    public function appInstalled(EventInterface $event): Response
    {
        /** @var AppLifecycleHandlerInterface $handler */
        foreach ($this->handlers as $handler) {
            $handler->appInstalled($event);
        }

        return new Response();
    }

    /**
     * @Route("/applifecycle/updated", name="applifecycle.updated", methods={"POST"})
     * The event `app.updated` gets triggered each time a shop updates your app.
     */
    public function appUpdated(EventInterface $event): Response
    {
        /** @var AppLifecycleHandlerInterface $handler */
        foreach ($this->handlers as $handler) {
            $handler->appUpdated($event);
        }

        return new Response();
    }

    /**
     * @Route("applifecycle/activated", name="applifecycle.activated", methods={"POST"})
     * The event `app.activated` gets triggered each time your app gets activated.
     * This also happens after your app is installed.
     */
    public function appActivated(EventInterface $event): Response
    {
        /** @var AppLifecycleHandlerInterface $handler */
        foreach ($this->handlers as $handler) {
            $handler->appActivated($event);
        }

        return new Response();
    }

    /**
     * @Route("/applifecycle/deactivated", name="applifecycle.deactivated", methods={"POST"})
     * The event `app.deactivated` gets triggered each time your app gets deactivated.
     * This don't happen when your app gets uninstalled.
     */
    public function appDeactivated(EventInterface $event): Response
    {
        /** @var AppLifecycleHandlerInterface $handler */
        foreach ($this->handlers as $handler) {
            $handler->appDeactivated($event);
        }

        return new Response();
    }

    /**
     * @Route("/applifecycle/deleted", name="applifecycle.deleted", methods={"POST"})
     * The event `app.deleted` gets triggered each time your app gets uninstalled.
     */
    public function appDeleted(EventInterface $event): Response
    {
        /** @var AppLifecycleHandlerInterface $handler */
        foreach ($this->handlers as $handler) {
            $handler->appDeleted($event);
        }

        $shopId = $event->getShopId();
        $shop = $this->shopRepository->find($shopId);

        if (null === $shop) {
            throw new ShopNotFoundException($shopId);
        }

        $this->entityManager->remove($shop);
        $this->entityManager->flush();

        return new Response();
    }
}
