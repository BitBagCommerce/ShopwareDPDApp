<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Calculator;

use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

interface OrderWeightCalculatorInterface
{
    public function calculate(OrderEntity $order, Context $context): float;
}
