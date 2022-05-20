<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use T3ko\Dpd\Request\GeneratePackageNumbersRequest;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

interface CreatePackageRequestFactoryInterface
{
    public function create(string $shopId, OrderEntity $order, Context $context): GeneratePackageNumbersRequest;
}
