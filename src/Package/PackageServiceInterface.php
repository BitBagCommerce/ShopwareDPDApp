<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Package;

use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

interface PackageServiceInterface
{
    public function create(OrderEntity $order, string $shopId, Context $context): void;
}
