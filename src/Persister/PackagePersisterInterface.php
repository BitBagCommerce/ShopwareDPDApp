<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Persister;

use BitBag\ShopwareDpdApp\Entity\Package;

interface PackagePersisterInterface
{
    public function saveOrderNumber(Package $package, string $orderCourierNumber): void;
}
