<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\Repository;

use BitBag\ShopwareAppSkeleton\Entity\Shop;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ShopRepository extends ServiceEntityRepository implements ShopRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Shop::class);
    }

    public function findSecretByShopId(string $shopId): ?string
    {
        $queryBuilder = $this->createQueryBuilder('shop');
        $queryBuilder
            ->select('shop_secret')
            ->from('shop', 's')
            ->where('s.shop_id = :shop_id')
            ->setParameter('s.shop_id', $shopId);

        /* @var ?string */
        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}
