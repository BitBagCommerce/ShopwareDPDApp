<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Api;

use T3ko\Dpd\Api;

interface ApiServiceInterface
{
    public function getApi(string $shopId): Api;
}
