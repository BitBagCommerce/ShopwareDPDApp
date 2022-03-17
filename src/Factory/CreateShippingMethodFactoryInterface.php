<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use BitBag\ShopwareDpdApp\AppSystem\Client\ClientInterface;

interface CreateShippingMethodFactoryInterface
{
    public function create(string $ruleId, array $deliveryTime, ClientInterface $client): void;
}
