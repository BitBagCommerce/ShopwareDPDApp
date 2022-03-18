<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Calculator;

final class OrderWeightCalculator implements OrderWeightCalculatorInterface
{
    public function calculate(array $lineItems): float
    {
        $totalWeight = 0;

        foreach ($lineItems as $item) {
            $weight = $item['quantity'] * $item['product']['weight'];
            $totalWeight += $weight;
        }

        return $totalWeight;
    }
}
