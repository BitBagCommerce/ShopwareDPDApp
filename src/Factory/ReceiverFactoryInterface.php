<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use T3ko\Dpd\Objects\Receiver;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

interface ReceiverFactoryInterface
{
    public function create(OrderEntity $order, string $currencyCode): Receiver;
}
