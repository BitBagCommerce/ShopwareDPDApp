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
        $result = $this->createQueryBuilder('o')
                       ->where('o.orderId = :orderId')
                       ->andWhere('o.parcelId IS NOT NULL')
                       ->setParameter('orderId', $orderId)
                       ->setMaxResults(1)
                       ->getQuery()
                       ->getOneOrNullResult();

        if (null === $result) {
            throw new PackageException('bitbag.shopware_dpd_app.package.not_found');
        }

        return $result;
    }

    public function findByOrderId(string $orderId): ?Package
    {
        return $this->createQueryBuilder('o')
                    ->where('o.orderId = :orderId')
                    ->andWhere('o.parcelId IS NOT NULL')
                    ->setParameter('orderId', $orderId)
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    public function findOrdersWithoutOrderCourier(): array
    {
        return $this->createQueryBuilder('p')
                    ->where('p.orderCourierNumber IS NULL')
                    ->andWhere('p.waybill IS NOT NULL')
                    ->orderBy('p.parcelId', 'DESC')
                    ->getQuery()
                    ->getResult();
    }
}
