<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Api;

use BitBag\ShopwareDpdApp\Entity\OrderCourier;
use BitBag\ShopwareDpdApp\Entity\Package;
use BitBag\ShopwareDpdApp\Exception\PackageException;
use BitBag\ShopwareDpdApp\Factory\OrderCourier\PackagePickupFactoryInterface;
use BitBag\ShopwareDpdApp\Model\OrderCourierPackageDetailsModel;
use BitBag\ShopwareDpdApp\Resolver\ApiClientResolverInterface;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

final class OrderCourierService implements OrderCourierServiceInterface
{
    private ApiClientResolverInterface $apiClientResolver;

    private PackagePickupFactoryInterface $packagePickupFactory;

    public function __construct(
        ApiClientResolverInterface $apiClientResolver,
        PackagePickupFactoryInterface $packagePickupFactory
    ) {
        $this->apiClientResolver = $apiClientResolver;
        $this->packagePickupFactory = $packagePickupFactory;
    }

    public function orderCourierByPackages(
        array $orders,
        array $packages,
        string $shopId,
        OrderCourier $orderCourier,
        Context $context
    ): array {
        $api = $this->apiClientResolver->getClient($shopId);

        $return = [];

        /** @var OrderEntity $order */
        foreach ($orders as $order) {
            $package = current(array_filter($packages, function (Package $package) use ($order) {
                return $package->getOrderId() === $order->id;
            }));

            if (null === $package) {
                throw new PackageException('bitbag.shopware_dpd_app.package.not_found');
            }

            $request = $this->packagePickupFactory->create(
                $shopId,
                $order,
                $orderCourier,
                $api,
                $context
            );

            $pickupRequest = $api->getPickupRequest($request);

            $orderCourierNumer = $pickupRequest->getReturn()->getOrderNumber();

            $return[] = new OrderCourierPackageDetailsModel($package, $orderCourierNumer);
        }

        return $return;
    }
}
