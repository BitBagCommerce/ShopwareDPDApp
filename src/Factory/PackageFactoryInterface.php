<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use T3ko\Dpd\Objects\Package;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

interface PackageFactoryInterface
{
    public function create(
        string $shopId,
        OrderEntity $order,
        Context $context
    ): Package;
}
