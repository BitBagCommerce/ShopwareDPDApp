<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Model;

use BitBag\ShopwareDpdApp\Entity\Package;

interface OrderCourierPackageDetailsModelInterface
{
    public function getPackage(): Package;

    public function getOrderCourierNumber(): string;
}
