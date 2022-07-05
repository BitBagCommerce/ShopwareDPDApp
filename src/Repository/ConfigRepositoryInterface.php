<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Repository;

use BitBag\ShopwareDpdApp\Entity\ConfigInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

interface ConfigRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function getByShopId(string $shopId): ConfigInterface;

    public function findByShopIdAndSalesChannelId(string $shopId, string $salesChannelId): ?ConfigInterface;
}
