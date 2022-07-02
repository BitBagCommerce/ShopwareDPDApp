<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Finder;

interface PackageFinderInterface
{
    public function findOrdersWithoutOrderCourier(): array;
}
