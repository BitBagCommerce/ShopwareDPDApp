<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory\OrderCourier;

use BitBag\ShopwareDpdApp\Exception\Order\OrderException;
use T3ko\Dpd\Soap\Types\PickupCustomerDPPV1;
use Vin\ShopwareSdk\Data\Entity\OrderAddress\OrderAddressEntity;

final class PickupCustomer implements PickupCustomerInterface
{
    public function create(OrderAddressEntity $billingAddress): PickupCustomerDPPV1
    {
        $firstName = $billingAddress->firstName;
        $lastName = $billingAddress->lastName;
        $phoneNumber = $billingAddress->phoneNumber;

        if (null === $firstName || null === $lastName || null === $phoneNumber) {
            throw new OrderException('bitbag.shopware_dpd_app.order_courier.billing_address_not_found');
        }

        $pickupCustomer = new PickupCustomerDPPV1();
        $pickupCustomer->setCustomerFullName($firstName . ' ' . $lastName);
        $pickupCustomer->setCustomerName($firstName);
        $pickupCustomer->setCustomerPhone($phoneNumber);

        return $pickupCustomer;
    }
}
