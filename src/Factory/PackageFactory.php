<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use BitBag\ShopwareDpdApp\Api\ApiService;
use BitBag\ShopwareDpdApp\Exception\ConfigNotFoundException;
use BitBag\ShopwareDpdApp\Exception\ErrorNotificationException;
use BitBag\ShopwareDpdApp\Exception\Order\OrderException;
use BitBag\ShopwareDpdApp\Exception\PackageException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;
use Vin\ShopwareSdk\Repository\RepositoryInterface;

final class PackageFactory implements PackageFactoryInterface
{
    private EntityManagerInterface $entityManager;

    private RepositoryInterface $orderRepository;

    private CreatePackageRequestFactoryInterface $createPackageFactory;

    private ApiService $apiService;

    public function __construct(
        EntityManagerInterface $entityManager,
        RepositoryInterface $orderRepository,
        CreatePackageRequestFactoryInterface $createPackageFactory,
        ApiService $apiService
    ) {
        $this->entityManager = $entityManager;
        $this->orderRepository = $orderRepository;
        $this->createPackageFactory = $createPackageFactory;
        $this->apiService = $apiService;
    }

    public function create(OrderEntity $order, string $shopId, Context $context): int
    {
        try {
            $api = $this->apiService->getApi($shopId);
        } catch (ConfigNotFoundException $exception) {
            throw new ErrorNotificationException($exception->getMessage());
        }

        try {
            $request = $this->createPackageFactory->create($shopId, $order, $context);
        } catch (ErrorNotificationException $exception) {
            throw new ErrorNotificationException($exception->getMessage());
        } catch (OrderException $exception) {
            throw new ErrorNotificationException($exception->getMessage());
        } catch (PackageException $exception) {
            throw new ErrorNotificationException($exception->getMessage());
        }

        try {
            $response = $api->generatePackageNumbers($request);
        } catch (Exception) {
            throw new ErrorNotificationException('bitbag.shopware_dpd_app.package.errorWhileCreatePackage');
        }

        if (empty($response->getPackages()) || empty($response->getPackages()[0]->getParcels()) ||
            !$parcelId = $response->getPackages()[0]->getParcels()[0]->getId()
        ) {
            throw new ErrorNotificationException('bitbag.shopware_dpd_app.package.notFoundParcelId');
        }

        return $parcelId;
    }
}
