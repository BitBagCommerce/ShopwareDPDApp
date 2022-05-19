<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use Vin\ShopwareSdk\Repository\Struct\IdSearchResult;

interface ShippingMethodPayloadFactoryInterface
{
    public const SHIPPING_KEY = 'DPD';

    public function create(string $ruleId, string $currencyId, IdSearchResult $deliveryTime): array;
}
