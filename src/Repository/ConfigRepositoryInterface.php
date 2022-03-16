<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Repository;

use BitBag\ShopwareDpdApp\Entity\ConfigInterface;

interface ConfigRepositoryInterface
{
    public function findOneBy(array $criteria, ?array $orderBy = null);

    public function findByShopId(string $shopId): ?ConfigInterface;
}
