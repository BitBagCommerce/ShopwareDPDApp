<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory\OrderCourier;

use BitBag\ShopwareDpdApp\Entity\OrderCourier;
use T3ko\Dpd\Soap\Types\DpdPickupCallParamsV2;
use T3ko\Dpd\Soap\Types\PickupCallSimplifiedDetailsDPPV1;

interface DpdPickupParametersFactoryInterface
{
    public function create(
        OrderCourier $orderCourier,
        PickupCallSimplifiedDetailsDPPV1 $pickupDetails
    ): DpdPickupCallParamsV2;
}
