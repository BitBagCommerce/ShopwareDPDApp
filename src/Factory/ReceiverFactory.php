<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use BitBag\ShopwareDpdApp\Exception\OrderAddressException;
use BitBag\ShopwareDpdApp\Provider\Defaults;
use T3ko\Dpd\Objects\Receiver;
use Vin\ShopwareSdk\Data\Entity\OrderAddress\OrderAddressEntity;

final class ReceiverFactory implements ReceiverFactoryInterface
{
    public const PHONE_NUMBER_REGEX = "/(?:(?:\+|00)[0-9]{1,3})?(\d{9,12})/";

    public const PHONE_NUMBER_LENGTH = 9;

    public const POST_CODE_REGEX = "/^(\d{2})(-\d{3})?$/i";

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

        $phoneNumber = str_replace(['+48', '+', '-', ' '], '', $phoneNumber);

        $this->checkPhoneNumberValidity($phoneNumber);

        $postalCode = str_replace('-', '', $zipcode);

        $this->checkPostCodeValidity($postalCode);

        return new Receiver(
            $phoneNumber,
            $firstName . ' ' . $lastName,
            $street,
            $postalCode,
            $city,
            Defaults::LOCALE_CODE
        );
    }

    private function checkPhoneNumberValidity(string $phoneNumber): void
    {
        preg_match(self::PHONE_NUMBER_REGEX, $phoneNumber, $phoneNumberMatches);

        if ([] === $phoneNumberMatches) {
            throw new OrderAddressException('bitbag.shopware_dpd_app.order.address.phone_number_invalid');
        }

        $phoneNumberLength = strlen($phoneNumberMatches[0]);

        if (self::PHONE_NUMBER_LENGTH !== $phoneNumberLength) {
            throw new OrderAddressException('bitbag.shopware_dpd_app.order.address.phone_number_invalid');
        }
    }

    private function isPostCodeValid(string $postCode): bool
    {
        return (bool) preg_match(self::POST_CODE_REGEX, $postCode);
    }

    private function checkPostCodeValidity(string $postCode): void
    {
        if (!$this->isPostCodeValid($postCode)) {
            $postCode = trim(substr_replace($postCode, '-', 2, 0));

            if (!$this->isPostCodeValid($postCode)) {
                throw new OrderAddressException('bitbag.shopware_dpd_app.order.address.post_code_invalid');
            }
        }
    }
}
