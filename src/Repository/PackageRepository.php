<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Repository;

use BitBag\ShopwareDpdApp\Entity\Package;
use BitBag\ShopwareDpdApp\Exception\PackageException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class PackageRepository extends ServiceEntityRepository implements PackageRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Package::class);
    }

    public function getByOrderId(string $orderId): Package
    {
        $queryBuilder = $this->createQueryBuilder('o')
                             ->where('o.orderId = :orderId')
                             ->andWhere('o.parcelId IS NOT NULL')
                             ->setParameter('orderId', $orderId)
                             ->setMaxResults(1);

        $result = $queryBuilder
            ->getQuery()
            ->getOneOrNullResult();

        if (null === $result) {
            throw new PackageException('bitbag.shopware_dpd_app.package.not_found');
        }

        return $result;
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

    public function findOrdersIdsWithoutOrderCourier(): array
    {
        $queryBuilder = $this->createQueryBuilder('p')
                             ->select('p.orderId, p.id')
                             ->where('p.orderCourierNumber IS NULL')
                             ->andWhere('p.waybill IS NOT NULL')
                             ->orderBy('p.parcelId', 'DESC');

        return $queryBuilder
            ->getQuery()
            ->getScalarResult();
    }
}
