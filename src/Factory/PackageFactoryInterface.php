<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use BitBag\ShopwareDpdApp\Model\Order;

interface PackageFactoryInterface
{
    public function create(Order $orderModel): int;
}
