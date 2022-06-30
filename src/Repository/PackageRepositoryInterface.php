<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Repository;

use BitBag\ShopwareDpdApp\Entity\Package;
use Doctrine\Persistence\ObjectRepository;

interface PackageRepositoryInterface extends ObjectRepository
{
    public function getByOrderId(string $orderId): Package;

    public function findByOrderId(string $orderId): ?Package;

    public function findOrdersIdsWithoutOrderCourier(): array;
}
