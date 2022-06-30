<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory\OrderCourier;

use T3ko\Dpd\Soap\Types\PickupCustomerDPPV1;
use Vin\ShopwareSdk\Data\Entity\OrderAddress\OrderAddressEntity;

interface PickupCustomerInterface
{
    public function create(OrderAddressEntity $billingAddress): PickupCustomerDPPV1;
}
