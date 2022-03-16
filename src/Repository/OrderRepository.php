<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Repository;

use BitBag\ShopwareDpdApp\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

class OrderRepository extends ServiceEntityRepository implements OrderRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByOrderId(string $orderId): ?Order
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
