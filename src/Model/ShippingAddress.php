<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\Model;

class ShippingAddress implements ShippingAddressInterface, ModelValidInterface
{
    private array $dpdPackageData;

    private ?string $countryCode;

    public function __construct(array $dpdPackageData, ?string $countryCode)
    {
        $this->dpdPackageData = $dpdPackageData;
        $this->countryCode = $countryCode;
    }

    public function getFirstName(): ?string
    {
        return $this->dpdPackageData['firstName'] ?? null;
    }

    public function getLastName(): ?string
    {
        return $this->dpdPackageData['lastName'] ?? null;
    }

    public function getStreet(): ?string
    {
        return $this->dpdPackageData['street'] ?? null;
    }

    public function getZipCode(): ?string
    {
        $zipCode = $this->dpdPackageData['zipcode'] ?? null;

        return str_replace(
            [
                '-',
                ' ',
            ],
            '',
            $zipCode
        );
    }

    public function getCity(): ?string
    {
        return $this->dpdPackageData['city'] ?? null;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->dpdPackageData['phoneNumber'] ?? null;
    }

    public function isValid(): bool
    {
        return $this->getFirstName() &&
            $this->getLastName() &&
            $this->getPhoneNumber() &&
            $this->getStreet() &&
            $this->getZipCode() &&
            $this->getCity() &&
            $this->getCountryCode();
    }
}
