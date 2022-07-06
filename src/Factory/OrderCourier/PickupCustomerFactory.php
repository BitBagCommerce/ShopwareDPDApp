<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory\OrderCourier;

use BitBag\ShopwareDpdApp\Exception\Order\OrderAddressException;
use T3ko\Dpd\Soap\Types\PickupCustomerDPPV1;
use Vin\ShopwareSdk\Data\Entity\OrderAddress\OrderAddressEntity;

final class PickupCustomerFactory implements PickupCustomerFactoryInterface
{
    public function create(OrderAddressEntity $billingAddress): PickupCustomerDPPV1
    {
        $firstName = $billingAddress->firstName;
        $lastName = $billingAddress->lastName;
        $phoneNumber = $billingAddress->phoneNumber;

        if (null === $firstName) {
            throw new OrderAddressException('bitbag.shopware_dpd_app.order.address.first_name_empty');
        }

        if (null === $lastName) {
            throw new OrderAddressException('bitbag.shopware_dpd_app.order.address.last_name_empty');
        }

        if (null === $phoneNumber) {
            throw new OrderAddressException('bitbag.shopware_dpd_app.order.address.phone_number_empty');
        }

        $pickupCustomer = new PickupCustomerDPPV1();
        $pickupCustomer->setCustomerFullName($firstName . ' ' . $lastName);
        $pickupCustomer->setCustomerName($firstName);
        $pickupCustomer->setCustomerPhone($phoneNumber);

        return $pickupCustomer;
    }
}
