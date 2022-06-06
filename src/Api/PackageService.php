<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Api;

use BitBag\ShopwareDpdApp\Exception\ApiException;
use BitBag\ShopwareDpdApp\Exception\ErrorNotificationException;
use BitBag\ShopwareDpdApp\Exception\Order\OrderException;
use BitBag\ShopwareDpdApp\Exception\PackageException;
use BitBag\ShopwareDpdApp\Factory\PackageFactoryInterface;
use BitBag\ShopwareDpdApp\Provider\Defaults;
use T3ko\Dpd\Objects\RegisteredParcel;
use T3ko\Dpd\Request\GeneratePackageNumbersRequest;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

final class PackageService implements PackageServiceInterface
{
    private PackageFactoryInterface $packageFactory;

    private ApiClientResolverInterface $apiClientResolver;

    public function __construct(
        PackageFactoryInterface $packageFactory,
        ApiClientResolverInterface $apiClientResolver
    ) {
        $this->packageFactory = $packageFactory;
        $this->apiClientResolver = $apiClientResolver;
    }

    public function create(OrderEntity $order, string $shopId, Context $context): array
    {
        try {
            $package = $this->packageFactory->create($shopId, $order, $context);
        } catch (OrderException | PackageException $exception) {
            throw new ErrorNotificationException($exception->getMessage());
        }

        $api = $this->apiClientResolver->getApi($shopId);

        $singlePackageRequest = GeneratePackageNumbersRequest::fromPackage($package);

        try {
            $response = $api->generatePackageNumbers($singlePackageRequest);
        } catch (\Exception | ApiException $e) {
            if (Defaults::STATUS_DISALLOWED_FID === $e->getMessage() ||
                str_contains(Defaults::STATUS_INCORRECT_LOGIN_OR_PASSWORD, $e->getMessage()) ||
                str_contains(Defaults::STATUS_ACCOUNT_IS_LOCKED, $e->getMessage())
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
        $result = [];

        foreach ($packages as $package) {
            /** @var RegisteredParcel $registeredParcel */
            $registeredParcel = $package->getParcels()[0];

            $result[] = [
                'id' => $registeredParcel->getId(),
                'waybill' => $registeredParcel->getWaybill(),
            ];
        }

        return $result;
    }
}
