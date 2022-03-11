<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\Repository;

interface ConfigRepositoryInterface
{
    public function findOneBy(array $criteria, ?array $orderBy = null);
}
