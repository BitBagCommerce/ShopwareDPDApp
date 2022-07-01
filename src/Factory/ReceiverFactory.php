<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use BitBag\ShopwareDpdApp\Exception\Order\OrderAddressException;
use BitBag\ShopwareDpdApp\Exception\PackageException;
use BitBag\ShopwareDpdApp\Provider\Defaults;
use T3ko\Dpd\Objects\Receiver;
use Vin\ShopwareSdk\Data\Entity\OrderAddress\OrderAddressEntity;

final class ReceiverFactory implements ReceiverFactoryInterface
{
    public const PHONE_NUMBER_REGEX = "/(?:(?:\+|00)[0-9]{1,3})?(\d{9,12})/";

    public const PHONE_NUMBER_LENGTH = 9;

    public function create(?OrderAddressEntity $address): Receiver
    {
        if (null === $address) {
            throw new OrderAddressException('bitbag.shopware_dpd_app.order.shipping_address_not_found');
        }

        $phoneNumber = $address->phoneNumber;
        $firstName = $address->firstName;
        $lastName = $address->lastName;
        $street = $address->street;
        $zipcode = $address->zipcode;
        $city = $address->city;

        if (null === $phoneNumber) {
            throw new OrderAddressException('bitbag.shopware_dpd_app.order.address.phone_number_empty');
        }

        if (null === $firstName) {
            throw new OrderAddressException('bitbag.shopware_dpd_app.order.address.first_name_empty');
        }

        if (null === $lastName) {
            throw new OrderAddressException('bitbag.shopware_dpd_app.order.address.last_name_empty');
        }

        if (null === $street) {
            throw new OrderAddressException('bitbag.shopware_dpd_app.order.address.street_empty');
        }

        if (null === $zipcode) {
            throw new OrderAddressException('bitbag.shopware_dpd_app.order.address.zip_code_empty');
        }

        if (null === $city) {
            throw new OrderAddressException('bitbag.shopware_dpd_app.order.address.city_empty');
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

        $phoneNumberLength = strlen($phoneNumberMatches[0]);

        if ([] === $phoneNumberMatches || self::PHONE_NUMBER_LENGTH !== $phoneNumberLength) {
            throw new PackageException('bitbag.shopware_dpd_app.order.address.phone_number_invalid');
        }
    }
}
