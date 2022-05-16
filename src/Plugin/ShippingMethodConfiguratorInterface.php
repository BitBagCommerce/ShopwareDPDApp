<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Plugin;

use Vin\ShopwareSdk\Data\Context;

interface ShippingMethodConfiguratorInterface
{
    public function createShippingMethod(Context $context): void;
}
