<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\AppSystem\Resolver;

use BitBag\ShopwareAppSkeleton\AppSystem\Credentials\CredentialsInterface;

interface CredentialsResolverInterface
{
    public function resolveByShopId(string $shopId): CredentialsInterface;
}
