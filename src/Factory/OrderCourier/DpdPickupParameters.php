<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory\OrderCourier;

use BitBag\ShopwareDpdApp\Entity\OrderCourier;
use T3ko\Dpd\Soap\Types\DpdPickupCallParamsV2;
use T3ko\Dpd\Soap\Types\PickupCallSimplifiedDetailsDPPV1;

final class DpdPickupParameters implements DpdPickupParametersInterface
{
    public function create(
        OrderCourier $orderCourier,
        PickupCallSimplifiedDetailsDPPV1 $pickupDetails
    ): DpdPickupCallParamsV2 {
        $params = new DpdPickupCallParamsV2();
        $params->setPickupDate($orderCourier->getPickupDate());
        $params->setPickupTimeFrom($orderCourier->getPickupTimeFrom());
        $params->setPickupTimeTo($orderCourier->getPickupTimeTo());
        $params->setOperationType('INSERT');
        $params->setOrderType('DOMESTIC');
        $params->setWaybillsReady(true);
        $params->setPickupCallSimplifiedDetails($pickupDetails);

        return $params;
    }
}
