<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Repository;

use BitBag\ShopwareDpdApp\Entity\Package;

/**
 * @method Package|null find($id, $lockMode = null, $lockVersion = null)
 * @method Package|null findOneBy(array $criteria, array $orderBy = null)
 * @method Package[]    findAll()
 * @method Package[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface PackageRepositoryInterface
{
    public function getByOrderId(string $orderId): Package;

    public function findByOrderId(string $orderId): ?Package;

    public function findOrdersWithoutOrderCourier(): array;
}
