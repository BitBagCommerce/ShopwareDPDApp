<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

interface RulePackageFactoryInterface
{
    public function create(string $name, string $paymentMethodId): array;
}
