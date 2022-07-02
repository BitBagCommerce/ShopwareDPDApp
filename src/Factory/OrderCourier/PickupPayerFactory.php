<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory\OrderCourier;

use BitBag\ShopwareDpdApp\Entity\ConfigInterface;
use T3ko\Dpd\Soap\Types\PickupPayerDPPV1;

final class PickupPayerFactory implements PickupPayerFactoryInterface
{
    public function create(int $masterFid, ConfigInterface $config): PickupPayerDPPV1
    {
        $pickupPayer = new PickupPayerDPPV1();
        $pickupPayer->setPayerNumber($masterFid);
        $pickupPayer->setPayerName($config->getSenderFirstLastName());
        $pickupPayer->setPayerCostCenter($config->getSenderFirstLastName());

        return $pickupPayer;
    }
}
