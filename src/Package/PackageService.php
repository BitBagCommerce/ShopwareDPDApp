<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Package;

use BitBag\ShopwareDpdApp\Api\ApiService;
use BitBag\ShopwareDpdApp\Api\WebClientInterface;
use BitBag\ShopwareDpdApp\Entity\Package as PackageEntity;
use BitBag\ShopwareDpdApp\Exception\ErrorNotificationException;
use BitBag\ShopwareDpdApp\Exception\Order\OrderException;
use BitBag\ShopwareDpdApp\Exception\PackageException;
use BitBag\ShopwareDpdApp\Factory\PackageRequestFactoryInterface;
use BitBag\ShopwareDpdApp\Repository\ConfigRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

final class PackageService implements PackageServiceInterface
{
    private PackageRequestFactoryInterface $packageRequestFactory;

    private EntityManagerInterface $entityManager;

    private ConfigRepositoryInterface $configRepository;

    public function __construct(
        PackageRequestFactoryInterface $packageRequestFactory,
        EntityManagerInterface $entityManager,
        ConfigRepositoryInterface $configRepository
    ) {
        $this->packageRequestFactory = $packageRequestFactory;
        $this->entityManager = $entityManager;
        $this->configRepository = $configRepository;
    }

    public function create(OrderEntity $order, string $shopId, Context $context): void
    {
        try {
            $packageRequest = $this->packageRequestFactory->create($shopId, $order, $context);
        } catch (OrderException | PackageException $exception) {
            throw new ErrorNotificationException($exception->getMessage());
        }

        $config = $this->configRepository->getByShopId($shopId);

        $api = new ApiService(
            $config->getApiLogin(),
            $config->getApiPassword(),
            $config->getApiFid(),
            WebClientInterface::SANDBOX_ENVIRONMENT === $config->getApiEnvironment()
        );

        try {
            $response = $api->generatePackageNumbers($packageRequest);
        } catch (Exception $exception) {
            throw new ErrorNotificationException($exception->getMessage());
        }

        $package = $response->getPackages();

        if (empty($package) || empty($package[0]->getParcels())) {
            throw new ErrorNotificationException('bitbag.shopware_dpd_app.parcel.not_found');
        }

        $parcel = $package[0]->getParcels()[0];

        $packageEntity = new PackageEntity();
        $packageEntity->setShopId($shopId);
        $packageEntity->setOrderId($order->id);
        $packageEntity->setParcelId($parcel->getId());
        $packageEntity->setWaybill($parcel->getWaybill());

        $this->entityManager->persist($packageEntity);
        $this->entityManager->flush();
    }
}
