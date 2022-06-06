<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use BitBag\ShopwareDpdApp\Calculator\OrderWeightCalculatorInterface;
use BitBag\ShopwareDpdApp\Exception\PackageException;
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

        $width = $orderCustomFieldsResolver['width'];
        $height = $orderCustomFieldsResolver['height'];
        $depth = $orderCustomFieldsResolver['depth'];

        $sumPackage = $width + $height + $depth;

        if (self::MAX_WIDTH_AVAILABLE <= $width || self::MAX_SUM_SIZE <= $sumPackage) {
            throw new PackageException('bitbag.shopware_dpd_app.package.too_large');
        }

        return new Parcel($width, $height, $depth, $weight);
    }
}
