<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Validator;

use BitBag\ShopwareDpdApp\Model\OrderModelInterface;

interface ValidateOrderModelInterface
{
    public function validate(array $order, string $orderId, string $shopId): OrderModelInterface;
}
