<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use T3ko\Dpd\Objects\Parcel;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

interface ParcelFactoryInterface
{
    public function create(OrderEntity $order, Context $context): Parcel;
}
