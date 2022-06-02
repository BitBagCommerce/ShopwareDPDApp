<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Resolver;

use BitBag\ShopwareDpdApp\Exception\PackageException;
use BitBag\ShopwareDpdApp\Factory\CustomFieldsForPackageDetailsPayloadFactoryInterface;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

final class OrderCustomFieldsResolver implements OrderCustomFieldsResolverInterface
{
    public function resolve(OrderEntity $order): array
    {
        $packageDetailsKey = CustomFieldsForPackageDetailsPayloadFactoryInterface::PACKAGE_DETAILS_KEY;

        /**
         * @psalm-var array<array-key, mixed>|null
         */
        $orderCustomFields = $order->getCustomFields();

        if (null === $orderCustomFields) {
            throw new PackageException('bitbag.shopware_dpd_app.package.fill_required_custom_fields');
        }

        $depthKey = $packageDetailsKey . '_depth';
        $heightKey = $packageDetailsKey . '_height';
        $widthKey = $packageDetailsKey . '_width';
        $insuranceKey = $packageDetailsKey . '_insurance';

        if (!isset($orderCustomFields[$depthKey], $orderCustomFields[$heightKey], $orderCustomFields[$widthKey])) {
            throw new PackageException('package.fillRequiredCustomFields');
        }

        return [
            'depth' => $orderCustomFields[$depthKey],
            'height' => $orderCustomFields[$heightKey],
            'width' => $orderCustomFields[$widthKey],
            'insurance' => $orderCustomFields[$insuranceKey] ?? null,
        ];
    }
}
