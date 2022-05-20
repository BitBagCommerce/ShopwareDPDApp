<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Resolver;

use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

interface OrderCustomFieldsResolverInterface
{
    public function resolve(OrderEntity $order): array;
}
