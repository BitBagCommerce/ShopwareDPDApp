<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Validator;

interface ConfigValidatorInterface
{
    public function validateApiData(string $shopId, string $orderId): void;
}
