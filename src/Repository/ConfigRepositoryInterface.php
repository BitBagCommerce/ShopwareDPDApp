<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Repository;

use BitBag\ShopwareDpdApp\Entity\ConfigInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

interface ConfigRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function findOneBy(array $criteria, ?array $orderBy = null);

    public function findByShopId(string $shopId): ?ConfigInterface;
}
