<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory\OrderCourier;

use T3ko\Dpd\Soap\Types\PickupPackagesParamsDPPV1;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

interface PickupParametersInterface
{
    public function create(OrderEntity $order, Context $context): PickupPackagesParamsDPPV1;
}
