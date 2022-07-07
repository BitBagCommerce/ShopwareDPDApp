<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Resolver;

use T3ko\Dpd\Api;

interface ApiClientResolverInterface
{
    public function getApi(string $shopId, string $salesChannelId): Api;
}
