<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Repository;

use BitBag\ShopwareDpdApp\Entity\OrderCourier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class OrderCourierRepository extends ServiceEntityRepository implements OrderCourierRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderCourier::class);
    }
}
