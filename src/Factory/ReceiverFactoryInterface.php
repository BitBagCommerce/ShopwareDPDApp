<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use T3ko\Dpd\Objects\Receiver;
use Vin\ShopwareSdk\Data\Entity\OrderAddress\OrderAddressEntity;

interface ReceiverFactoryInterface
{
    public function create(OrderAddressEntity $address): Receiver;
}
