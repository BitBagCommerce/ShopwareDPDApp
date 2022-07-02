<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory\OrderCourier;

use BitBag\ShopwareDpdApp\Calculator\OrderWeightCalculatorInterface;
use BitBag\ShopwareDpdApp\Resolver\OrderCustomFieldsResolverInterface;
use T3ko\Dpd\Soap\Types\PickupPackagesParamsDPPV1;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

final class PickupParametersFactory implements PickupParametersFactoryInterface
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

    public function create(OrderEntity $order, Context $context): PickupPackagesParamsDPPV1
    {
        $resolvedFields = $this->orderCustomFieldsResolver->resolve($order);
        $parcelMaxWeight = $this->orderWeightCalculator->calculate($order, $context);

        $pickupParams = new PickupPackagesParamsDPPV1();
        $pickupParams->setDox(false);
        $pickupParams->setDoxCount(0);
        $pickupParams->setStandardParcel(true);
        $pickupParams->setPallet(false);
        $pickupParams->setPalletMaxHeight(0);
        $pickupParams->setPalletsCount(0);
        $pickupParams->setParcelsCount(1);
        $pickupParams->setParcelsWeight($parcelMaxWeight);
        $pickupParams->setParcelMaxWeight($parcelMaxWeight);
        $pickupParams->setParcelMaxWidth($resolvedFields['width']);
        $pickupParams->setParcelMaxHeight($resolvedFields['height']);
        $pickupParams->setParcelMaxDepth($resolvedFields['depth']);

        return $pickupParams;
    }
}
