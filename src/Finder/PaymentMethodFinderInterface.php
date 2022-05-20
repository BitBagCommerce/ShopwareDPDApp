<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Finder;

use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Repository\Struct\IdSearchResult;

interface PaymentMethodFinderInterface
{
    public function find(string $value, Context $context): IdSearchResult;
}
