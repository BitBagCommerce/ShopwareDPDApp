<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory\OrderCourier;

use BitBag\ShopwareDpdApp\Entity\ConfigInterface;
use T3ko\Dpd\Soap\Types\PickupSenderDPPV1;

interface PickupSenderInterface
{
    public function create(ConfigInterface $config): PickupSenderDPPV1;
}
