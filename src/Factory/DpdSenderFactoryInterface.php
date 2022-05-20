<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use T3ko\Dpd\Objects\Sender;

interface DpdSenderFactoryInterface
{
    public function create(string $shopId): Sender;
}
