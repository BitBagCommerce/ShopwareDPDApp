<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Api;

use BitBag\ShopwareDpdApp\Entity\OrderCourier;
use BitBag\ShopwareDpdApp\Entity\Package;
use BitBag\ShopwareDpdApp\Exception\PackageException;
use BitBag\ShopwareDpdApp\Factory\OrderCourier\PackagePickupFactoryInterface;
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
            $package = $this->searchPackageByOrderId($order->id, $packages);

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

            $return[] = [
                'package' => $package,
                'orderCourierNumber' => $orderCourierNumer,
            ];
        }

        return $return;
    }

    private function searchPackageByOrderId(string $orderId, array $packages): ?Package
    {
        /** @var Package $package */
        foreach ($packages as $package) {
            if ($package->getOrderId() === $orderId) {
                return $package;
            }
        }

        return null;
    }
}
