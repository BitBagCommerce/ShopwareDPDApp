<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Calculator;

use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

interface OrderWeightCalculatorInterface
{
    public const MAX_WEIGHT_AVAILABLE = 50;

    public function calculate(OrderEntity $order, Context $context): float;
}
