<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use BitBag\ShopwareDpdApp\Calculator\OrderWeightCalculatorInterface;
use BitBag\ShopwareDpdApp\Resolver\OrderCustomFieldsResolverInterface;
use T3ko\Dpd\Objects\Parcel;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

final class ParcelFactory implements ParcelFactoryInterface
{
    private OrderCustomFieldsResolverInterface $orderCustomFieldsResolver;

    private OrderWeightCalculatorInterface $orderWeightCalculator;

    public function __construct(
        OrderCustomFieldsResolverInterface $orderCustomFieldsResolver,
        OrderWeightCalculatorInterface $orderWeightCalculator
    ) {
        $this->orderCustomFieldsResolver = $orderCustomFieldsResolver;
        $this->orderWeightCalculator = $orderWeightCalculator;
    }

    public function create(OrderEntity $order, Context $context): Parcel
    {
        $orderCustomFieldsResolver = $this->orderCustomFieldsResolver->resolve($order);

        $weight = $this->orderWeightCalculator->calculate($order, $context);

        return new Parcel(
            $orderCustomFieldsResolver['width'],
            $orderCustomFieldsResolver['height'],
            $orderCustomFieldsResolver['depth'],
            $weight
        );
    }
}
