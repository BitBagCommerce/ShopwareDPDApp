<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Package;

use BitBag\ShopwareDpdApp\Api\ApiServiceInterface;
use BitBag\ShopwareDpdApp\Api\WebClientInterface;
use BitBag\ShopwareDpdApp\Entity\Package as PackageEntity;
use BitBag\ShopwareDpdApp\Exception\ErrorNotificationException;
use BitBag\ShopwareDpdApp\Exception\Order\OrderException;
use BitBag\ShopwareDpdApp\Exception\PackageException;
use BitBag\ShopwareDpdApp\Factory\PackageRequestFactoryInterface;
use BitBag\ShopwareDpdApp\Repository\ConfigRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use stdClass;
use T3ko\Dpd\Soap\Types\ParcelPGRV2;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

final class PackageService implements PackageServiceInterface
{
    private PackageRequestFactoryInterface $packageRequestFactory;

    private EntityManagerInterface $entityManager;

    private ConfigRepositoryInterface $configRepository;

    private ApiServiceInterface $apiService;

    public function __construct(
        PackageRequestFactoryInterface $packageRequestFactory,
        EntityManagerInterface $entityManager,
        ConfigRepositoryInterface $configRepository,
        ApiServiceInterface $apiService
    ) {
        $this->packageRequestFactory = $packageRequestFactory;
        $this->entityManager = $entityManager;
        $this->configRepository = $configRepository;
        $this->apiService = $apiService;
    }

    public function create(OrderEntity $order, string $shopId, Context $context): void
    {
        try {
            $packageRequest = $this->packageRequestFactory->create($shopId, $order, $context);
        } catch (OrderException | PackageException $exception) {
            throw new ErrorNotificationException($exception->getMessage());
        }

        $config = $this->configRepository->getByShopId($shopId);

        $api = $this->apiService;

        $api->setAuthData(
            $config->getApiLogin(),
            $config->getApiPassword(),
            $config->getApiFid(),
            WebClientInterface::SANDBOX_ENVIRONMENT === $config->getApiEnvironment()
        );

        try {
            $response = $api->createPackages($packageRequest);
        } catch (\Exception $exception) {
            throw new ErrorNotificationException($exception->getMessage());
        }

        /** @var stdClass $responsePackages */
        $responsePackages = $response->getReturn()->getPackages();

        $packages = $this->getPackagesFromResponse($responsePackages->Package);

        if (empty($packages)) {
            throw new ErrorNotificationException('bitbag.shopware_dpd_app.package.not_found');
        }

        $packageEntity = new PackageEntity();
        $packageEntity->setShopId($shopId);
        $packageEntity->setOrderId($order->id);
        $packageEntity->setParcelId($packages[0]['id']);
        $packageEntity->setWaybill($packages[0]['waybill']);

        $this->entityManager->persist($packageEntity);
        $this->entityManager->flush();
    }

    private function getPackagesFromResponse(array $packages): array
    {
        $packagesArr = [];

        foreach ($packages as $package) {
            /** @var ParcelPGRV2[] $parcels */
            $parcels = $package->getParcels()->Parcel;

            $packagesArr[] = [
                'id' => $parcels[0]->getParcelId(),
                'waybill' => $parcels[0]->getWaybill(),
            ];
        }

        return $packagesArr;
    }
}
