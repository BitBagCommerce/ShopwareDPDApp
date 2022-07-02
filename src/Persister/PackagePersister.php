<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Persister;

use BitBag\ShopwareDpdApp\Entity\Package;
use Doctrine\ORM\EntityManagerInterface;

final class PackagePersister implements PackagePersisterInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function saveOrderNumber(Package $package, string $orderCourierNumber): void
    {
        $package->setOrderCourierNumber($orderCourierNumber);

        $this->entityManager->persist($package);
        $this->entityManager->flush();
    }
}
