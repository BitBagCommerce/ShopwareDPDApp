<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use BitBag\ShopwareDpdApp\Exception\Order\OrderException;
use T3ko\Dpd\Objects\Receiver;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

final class ReceiverFactory implements ReceiverFactoryInterface
{
    public function create(OrderEntity $order, string $currencyCode): Receiver
    {
        $address = $order->addresses?->first();

        if (null === $address) {
            throw new OrderException('bitbag.shopware_dpd_app.order.shippingAddressNotFound');
        }

        $phoneNumber = $address->phoneNumber;
        $firstName = $address->firstName;
        $lastName = $address->lastName;
        $street = $address->street;
        $zipcode = $address->zipcode;
        $city = $address->city;

        if (null === $phoneNumber ||
            null === $firstName ||
            null === $lastName ||
            null === $street ||
            null === $zipcode ||
            null === $city
        ) {
            throw new OrderException('bitbag.shopware_dpd_app.order.shippingAddressValueInvalid');
        }

        return new Receiver(
            $phoneNumber,
            $firstName . ' ' . $lastName,
            $street,
            $zipcode,
            $city,
            $currencyCode
        );
    }
}
