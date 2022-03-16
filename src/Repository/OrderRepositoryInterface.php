<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Repository;

use BitBag\ShopwareDpdApp\Entity\Order;

interface OrderRepositoryInterface
{
    public function findByOrderId(string $orderId): ?Order;
}
