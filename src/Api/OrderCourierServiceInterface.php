<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Api;

use BitBag\ShopwareDpdApp\Entity\OrderCourier;
use Vin\ShopwareSdk\Data\Context;

interface OrderCourierServiceInterface
{
    public function orderCourierByPackages(
        array $orders,
        array $packages,
        string $shopId,
        OrderCourier $orderCourier,
        Context $context
    ): array;
}
