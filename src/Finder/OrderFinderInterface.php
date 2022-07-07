<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Finder;

use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderCollection;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

interface OrderFinderInterface
{
    public function getWithAssociations(?string $orderId, Context $context): OrderEntity;

    public function getOrdersByPackagesIds(array $packagesIds, Context $context): OrderCollection;
}
