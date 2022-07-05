<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Finder;

use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Repository\Struct\EntitySearchResult;

interface SalesChannelFinderInterface
{
    public function findAll(Context $context): EntitySearchResult;

    public function findById(string $id, Context $context): EntitySearchResult;
}
