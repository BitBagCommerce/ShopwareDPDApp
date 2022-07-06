<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use BitBag\ShopwareDpdApp\Calculator\OrderWeightCalculatorInterface;
use BitBag\ShopwareDpdApp\Exception\PackageException;
use BitBag\ShopwareDpdApp\Resolver\OrderCustomFieldResolverInterface;
use T3ko\Dpd\Objects\Parcel;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

final class ParcelFactory implements ParcelFactoryInterface
{
    private OrderCustomFieldResolverInterface $orderCustomFieldResolver;

    private OrderWeightCalculatorInterface $orderWeightCalculator;

    public function __construct(
        OrderCustomFieldResolverInterface $orderCustomFieldsResolver,
        OrderWeightCalculatorInterface $orderWeightCalculator
    ) {
        $this->orderCustomFieldResolver = $orderCustomFieldsResolver;
        $this->orderWeightCalculator = $orderWeightCalculator;
    }

    public function create(OrderEntity $order, Context $context): Parcel
    {
        $resolvedFields = $this->orderCustomFieldResolver->resolve($order);

        $weight = $this->orderWeightCalculator->calculate($order, $context);

        $width = $resolvedFields['width'];
        $height = $resolvedFields['height'];
        $depth = $resolvedFields['depth'];

        $sumPackage = $width + $height + $depth;

        if (self::MAX_WIDTH_AVAILABLE <= $width || self::MAX_SUM_SIZE <= $sumPackage) {
            throw new PackageException('bitbag.shopware_dpd_app.package.too_large');
        }

        return new Parcel(
            $width,
            $height,
            $depth,
            $weight,
            null,
            $resolvedFields['package_contents']
        );
    }
}
