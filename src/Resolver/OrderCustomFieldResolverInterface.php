<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Resolver;

use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

interface OrderCustomFieldResolverInterface
{
    public const PACKAGE_DETAILS_KEY = 'bitbag_shopware_dpd_app_package_details';

    public function resolve(OrderEntity $order): array;
}
