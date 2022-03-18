<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use BitBag\ShopwareDpdApp\Model\ShippingAddressModelInterface;
use T3ko\Dpd\Objects\Receiver;

final class CreateDpdReceiverFactory implements CreateDpdReceiverFactoryInterface
{
    public function create(ShippingAddressModelInterface $address): Receiver
    {
        return new Receiver(
            $address->getPhoneNumber(),
            $address->getFirstName().' '.$address->getLastName(),
            $address->getStreet(),
            $address->getZipCode(),
            $address->getCity(),
            $address->getCountryCode()
        );
    }
}
