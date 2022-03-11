<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\Model;

interface ShippingAddressInterface
{
    public function getFirstName(): ?string;

    public function getLastName(): ?string;

    public function getStreet(): ?string;

    public function getZipCode(): ?string;

    public function getCity(): ?string;

    public function getCountryCode(): ?string;

    public function getPhoneNumber(): ?string;
}
