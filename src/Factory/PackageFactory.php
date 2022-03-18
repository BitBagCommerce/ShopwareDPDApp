<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use BitBag\ShopwareDpdApp\Entity\Order as OrderEntity;
use BitBag\ShopwareDpdApp\Exception\ErrorNotificationException;
use BitBag\ShopwareDpdApp\Model\OrderModel;
use BitBag\ShopwareDpdApp\Repository\OrderRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final class PackageFactory implements PackageFactoryInterface
{
    private EntityManagerInterface $entityManager;

    private OrderRepositoryInterface $orderRepository;

    private CreatePackageFactoryInterface $createPackageFactory;

    public function __construct(
        EntityManagerInterface $entityManager,
        OrderRepositoryInterface $orderRepository,
        CreatePackageFactoryInterface $createPackageFactory
    ) {
        $this->entityManager = $entityManager;
        $this->orderRepository = $orderRepository;
        $this->createPackageFactory = $createPackageFactory;
    }

    public function create(OrderModel $orderModel): int
    {
        $shopId = $orderModel->getShopId();
        if (!$shopId) {
            throw new ErrorNotificationException('bitbag.shopware_dpd_app.shop.not_found');
        }

        $orderId = $orderModel->getOrderId();
        if (!$orderId) {
            throw new ErrorNotificationException('bitbag.shopware_dpd_app.order.not_found');
        }

        $order = $this->orderRepository->findByOrderId($orderId);
        if ($order) {
            if ($order->getParcelId()) {
                return $order->getParcelId();
            }
        } else {
            $order = new OrderEntity();
        }

        try {
            $parcelId = $this->createPackageFactory->create($orderModel);
        } catch (ErrorNotificationException $exception) {
            throw new ErrorNotificationException($exception->getMessage());
        }

        $order->setShopId($shopId);
        $order->setOrderId($orderId);
        $order->setParcelId($parcelId);

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $parcelId;
    }
}
