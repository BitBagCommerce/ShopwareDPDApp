<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use BitBag\ShopwareDpdApp\Exception\Order\OrderException;
use BitBag\ShopwareDpdApp\Exception\PackageException;
use BitBag\ShopwareDpdApp\Provider\Defaults;
use T3ko\Dpd\Objects\Receiver;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

final class ReceiverFactory implements ReceiverFactoryInterface
{
    public const PHONE_NUMBER_REGEX = "/(?:(?:\+|00)[0-9]{1,3})?(\d{9,12})/";

    public const PHONE_NUMBER_LENGTH = 9;

    public function create(OrderEntity $order): Receiver
    {
        $address = $order->addresses?->first();

        if (null === $address) {
            throw new OrderException('bitbag.shopware_dpd_app.order.shipping_address_not_found');
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
            throw new OrderException('bitbag.shopware_dpd_app.order.shipping_address_value_invalid');
        }

        $phoneNumber = str_replace(['+48', '+', '-', ' '], '', $phoneNumber);

        $this->checkPhoneNumberValidity($phoneNumber);

        return new Receiver(
            $phoneNumber,
            $firstName . ' ' . $lastName,
            $street,
            str_replace('-', '', $zipcode),
            $city,
            Defaults::CURRENCY_CODE
        );
    }

    private function checkPhoneNumberValidity(string $phoneNumber): void
    {
        preg_match(self::PHONE_NUMBER_REGEX, $phoneNumber, $phoneNumberMatches);

        if ([] === $phoneNumberMatches || self::PHONE_NUMBER_LENGTH !== strlen($phoneNumberMatches[0])) {
            throw new PackageException('bitbag.shopware_dpd_app.order.address.phone_number_invalid');
        }
    }
}
