<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use BitBag\ShopwareDpdApp\Model\OrderModelInterface;

interface CreatePackageFactoryInterface
{
    public function create(OrderModelInterface $orderModel): int;
}
