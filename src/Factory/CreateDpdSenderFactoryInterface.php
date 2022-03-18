<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use T3ko\Dpd\Objects\Sender;

interface CreateDpdSenderFactoryInterface
{
    public function create(string $shopId): Sender;
}
