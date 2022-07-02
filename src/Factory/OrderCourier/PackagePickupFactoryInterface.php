<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory\OrderCourier;

use BitBag\ShopwareDpdApp\Entity\OrderCourier;
use T3ko\Dpd\Api;
use T3ko\Dpd\Soap\Types\PackagesPickupCallV2Request;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

interface PackagePickupFactoryInterface
{
    public function create(
        string $shopId,
        OrderEntity $order,
        OrderCourier $orderCourier,
        Api $api,
        Context $context
    ): PackagesPickupCallV2Request;
}
