<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory\OrderCourier;

use BitBag\ShopwareDpdApp\Entity\ConfigInterface;
use T3ko\Dpd\Soap\Types\PickupPayerDPPV1;

interface PickupPayerFactoryInterface
{
    public function create(int $masterFid, ConfigInterface $config): PickupPayerDPPV1;
}
