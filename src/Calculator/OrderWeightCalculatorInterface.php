<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Calculator;

interface OrderWeightCalculatorInterface
{
    public function calculate(array $lineItems): float;
}
