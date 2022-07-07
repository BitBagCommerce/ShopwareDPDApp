<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Repository;

use BitBag\ShopwareDpdApp\Entity\Config;
use BitBag\ShopwareDpdApp\Entity\ConfigInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

/**
 * @method Config|null find($id, $lockMode = null, $lockVersion = null)
 * @method Config|null findOneBy(array $criteria, array $orderBy = null)
 * @method Config[]    findAll()
 * @method Config[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface ConfigRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function getByShopIdAndSalesChannelId(string $shopId, string $salesChannelId): ConfigInterface;

    public function findByShopIdAndSalesChannelId(string $shopId, string $salesChannelId): ?ConfigInterface;
}
