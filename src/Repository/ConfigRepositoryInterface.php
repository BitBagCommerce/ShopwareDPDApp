<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\Repository;

use BitBag\ShopwareAppSkeleton\Entity\ConfigInterface;

interface ConfigRepositoryInterface
{
    public function findOneBy(array $criteria, ?array $orderBy = null);

    public function findByShopId(string $shopId): ?ConfigInterface;
}
