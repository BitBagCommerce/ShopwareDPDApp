<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Resolver;

use BitBag\ShopwareDpdApp\Exception\PackageException;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

final class OrderCustomFieldsResolver implements OrderCustomFieldsResolverInterface
{
    public function resolve(OrderEntity $order): array
    {
        $packageDetailsKey = 'bitbag_inpost_point_package_details';

        /**
         * @psalm-var array<array-key, mixed>|null
         */
        $orderCustomFields = $order->getCustomFields();

        if (null === $orderCustomFields) {
            throw new PackageException('package.fillRequiredCustomFields');
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
