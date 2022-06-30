<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory\OrderCourier;

use BitBag\ShopwareDpdApp\Entity\ConfigInterface;
use T3ko\Dpd\Soap\Types\PickupSenderDPPV1;

final class PickupSender implements PickupSenderInterface
{
    public function create(ConfigInterface $config): PickupSenderDPPV1
    {
        $pickupSender = new PickupSenderDPPV1();
        $pickupSender->setSenderName($config->getSenderFirstLastName());
        $pickupSender->setSenderFullName($config->getSenderFirstLastName());
        $pickupSender->setSenderAddress($config->getSenderStreet());
        $pickupSender->setSenderPostalCode($config->getSenderZipCode());
        $pickupSender->setSenderPhone($config->getSenderPhoneNumber());
        $pickupSender->setSenderCity($config->getSenderCity());

        return $pickupSender;
    }
}
