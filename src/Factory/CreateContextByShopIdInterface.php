<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use Vin\ShopwareSdk\Data\Context;

interface CreateContextByShopIdInterface
{
    public function create(string $shopId): Context;
}
