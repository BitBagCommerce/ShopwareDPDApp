<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use BitBag\ShopwareDpdApp\Model\ShippingAddressModelInterface;
use T3ko\Dpd\Objects\Receiver;

interface CreateDpdReceiverFactoryInterface
{
    public function create(ShippingAddressModelInterface $address): Receiver;
}
