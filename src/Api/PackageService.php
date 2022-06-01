<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Api;

use BitBag\ShopwareDpdApp\Exception\ApiException;
use BitBag\ShopwareDpdApp\Exception\ErrorNotificationException;
use BitBag\ShopwareDpdApp\Exception\Order\OrderException;
use BitBag\ShopwareDpdApp\Exception\PackageException;
use BitBag\ShopwareDpdApp\Factory\PackageFactoryInterface;
use BitBag\ShopwareDpdApp\Repository\ConfigRepositoryInterface;
use T3ko\Dpd\Api;
use T3ko\Dpd\Objects\RegisteredParcel;
use T3ko\Dpd\Request\GeneratePackageNumbersRequest;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

final class PackageService implements PackageServiceInterface
{
    private PackageFactoryInterface $packageFactory;

    private ConfigRepositoryInterface $configRepository;

    public function __construct(
        PackageFactoryInterface $packageFactory,
        ConfigRepositoryInterface $configRepository,
    ) {
        $this->packageFactory = $packageFactory;
        $this->configRepository = $configRepository;
    }

    public function create(OrderEntity $order, string $shopId, Context $context): array
    {
        try {
            $package = $this->packageFactory->create($shopId, $order, $context);
        } catch (OrderException | PackageException $exception) {
            throw new ErrorNotificationException($exception->getMessage());
        }

        $config = $this->configRepository->getByShopId($shopId);

        $api = new Api(
            $config->getApiLogin(),
            $config->getApiPassword(),
            $config->getApiFid()
        );
        $api->setSandboxMode(WebClientInterface::SANDBOX_ENVIRONMENT === $config->getApiEnvironment());

        $singlePackageRequest = GeneratePackageNumbersRequest::fromPackage($package);

        try {
            $response = $api->generatePackageNumbers($singlePackageRequest);
        } catch (\Exception | ApiException $e) {
            if (ApiServiceInterface::DISALLOWED_FID === $e->getMessage() ||
                str_contains($e->getMessage(), ApiServiceInterface::INCORRECT_LOGIN_OR_PASSWORD) ||
                str_contains(ApiServiceInterface::ACCOUNT_IS_LOCKED, $e->getMessage())
            ) {
                throw new ErrorNotificationException('bitbag.shopware_dpd_app.api.provided_data_not_valid');
            }

            throw new ErrorNotificationException($e->getMessage());
        }

        $packages = $this->getPackagesFromResponse($response->getPackages());

        if (empty($packages)) {
            throw new ErrorNotificationException('bitbag.shopware_dpd_app.package.not_found');
        }

        return $packages;
    }

    private function getPackagesFromResponse(array $packages): array
    {
        $packagesArr = [];

        foreach ($packages as $package) {
            /** @var RegisteredParcel $registeredParcel */
            $registeredParcel = $package->getParcels()[0];

            $packagesArr[] = [
                'id' => $registeredParcel->getId(),
                'waybill' => $registeredParcel->getWaybill(),
            ];
        }

        return $packagesArr;
    }
}
