<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\Repository;

use BitBag\ShopwareAppSkeleton\Entity\Order;

interface OrderRepositoryInterface
{
    public function findByOrderId(string $orderId): ?Order;
}
