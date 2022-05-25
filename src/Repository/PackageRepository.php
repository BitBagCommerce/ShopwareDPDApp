<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Repository;

use BitBag\ShopwareDpdApp\Entity\Package;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class PackageRepository extends ServiceEntityRepository implements PackageRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Package::class);
    }

    public function findByOrderId(string $orderId): ?Package
    {
        $queryBuilder = $this->createQueryBuilder('o')
                             ->where('o.orderId = :orderId')
                             ->andWhere('o.parcelId IS NOT NULL')
                             ->setParameter('orderId', $orderId)
                             ->setMaxResults(1);

        return $queryBuilder
            ->getQuery()
            ->getOneOrNullResult();
    }
}
