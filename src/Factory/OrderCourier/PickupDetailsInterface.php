<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory\OrderCourier;

use T3ko\Dpd\Soap\Types\PickupCallSimplifiedDetailsDPPV1;
use T3ko\Dpd\Soap\Types\PickupCustomerDPPV1;
use T3ko\Dpd\Soap\Types\PickupPackagesParamsDPPV1;
use T3ko\Dpd\Soap\Types\PickupPayerDPPV1;
use T3ko\Dpd\Soap\Types\PickupSenderDPPV1;

interface PickupDetailsInterface
{
    public function create(
        PickupPayerDPPV1 $pickupPayer,
        PickupCustomerDPPV1 $pickupCustomer,
        PickupSenderDPPV1 $pickupSender,
        PickupPackagesParamsDPPV1 $packageParams
    ): PickupCallSimplifiedDetailsDPPV1;
}
