<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Api;

use BitBag\ShopwareDpdApp\Entity\OrderCourier;
use BitBag\ShopwareDpdApp\Entity\Package;
use BitBag\ShopwareDpdApp\Factory\OrderCourier\PackagePickupFactoryInterface;
use BitBag\ShopwareDpdApp\Model\OrderCourierPackageDetails;
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

        $packageDetails = [];

        /** @var OrderEntity $order */
        foreach ($orders as $order) {
            $packagesByOrderId = array_filter($packages, static function (Package $package) use ($order) {
                return $package->getOrderId() === $order->id;
            });

            /** @var Package $package */
            $package = array_shift($packagesByOrderId)[0];

            $request = $this->packagePickupFactory->create(
                $shopId,
                $order,
                $orderCourier,
                $api,
                $context
            );

            $pickupRequest = $api->getPickupRequest($request);
            $orderCourierNumer = $pickupRequest->getReturn()->getOrderNumber();
            $packageDetails[] = new OrderCourierPackageDetails($package, $orderCourierNumer);
        }

        return $packageDetails;
    }
}
