<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Finder;

use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Repository\Struct\IdSearchResult;

interface DeliveryTimeFinderInterface
{
    public function findDeliveryTimeByMinMax(int $min, int $max, Context $context): IdSearchResult;
}
