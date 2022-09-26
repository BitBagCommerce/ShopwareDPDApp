<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Model;

use BitBag\ShopwareDpdApp\Entity\Package;

final class OrderCourierPackageDetails implements OrderCourierPackageDetailsInterface
{
    private Package $package;

    private string $orderCourierNumber;

    public function __construct(Package $package, string $orderCourierNumber)
    {
        $this->package = $package;
        $this->orderCourierNumber = $orderCourierNumber;
    }

    public function getPackage(): Package
    {
        return $this->package;
    }

    public function getOrderCourierNumber(): string
    {
        return $this->orderCourierNumber;
    }
}
