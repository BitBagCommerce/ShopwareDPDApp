<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Repository;

use BitBag\ShopwareDpdApp\Entity\Config;
use BitBag\ShopwareDpdApp\Entity\ConfigInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

class ConfigRepository extends ServiceEntityRepository implements ConfigRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Config::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByShopId(string $shopId): ?ConfigInterface
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->leftJoin('c.shop', 'shop')
            ->where('shop.shopId = :shopId')
            ->setParameter('shopId', $shopId);

        return $queryBuilder
            ->getQuery()
            ->getOneOrNullResult();
    }
}
