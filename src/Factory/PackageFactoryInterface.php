<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use BitBag\ShopwareDpdApp\Model\OrderModel;

interface PackageFactoryInterface
{
    public function create(OrderModel $orderModel): int;
}
