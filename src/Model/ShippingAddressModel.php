<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Model;

use Symfony\Component\Validator\Constraints as Assert;

final class ShippingAddressModel implements ShippingAddressModelInterface
{
    /**
     * @Assert\NotBlank(message="bitbag.shopware_dpd_app.validator.order_model.shipping_address.first_name")
     */
    private string $firstName;

    /**
     * @Assert\NotBlank(message="bitbag.shopware_dpd_app.validator.order_model.shipping_address.last_name")
     */
    private string $lastName;

    /**
     * @Assert\NotBlank(message="bitbag.shopware_dpd_app.validator.order_model.shipping_address.street")
     */
    private string $street;

    /**
     * @Assert\NotBlank(message="bitbag.shopware_dpd_app.validator.order_model.shipping_address.zip_code")
     */
    private string $zipCode;

    /**
     * @Assert\NotBlank(message="bitbag.shopware_dpd_app.validator.order_model.shipping_address.city")
     */
    private string $city;

    /**
     * @Assert\NotBlank(message="bitbag.shopware_dpd_app.validator.order_model.shipping_address.phone_number")
     */
    private string $phoneNumber;

    /**
     * @Assert\NotBlank(message="bitbag.shopware_dpd_app.validator.order_model.shipping_address.country_code")
     */
    private string $countryCode;

    public function __construct(
        ?string $firstName,
        ?string $lastName,
        ?string $street,
        ?string $zipCode,
        ?string $city,
        ?string $countryCode,
        ?string $phoneNumber
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->street = $street;
        $this->zipCode = str_replace(['-', ' ',], '', $zipCode);
        $this->city = $city;
        $this->countryCode = $countryCode ? strtoupper($countryCode) : null;
        $this->phoneNumber = $phoneNumber;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }
}
