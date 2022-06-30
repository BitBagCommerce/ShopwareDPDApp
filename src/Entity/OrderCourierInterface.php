<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Entity;

interface OrderCourierInterface
{
    public function getId(): int;

    public function getPickupDate(): string;

    public function setPickupDate(string $pickupDate): void;

    public function getPickupTimeFrom(): string;

    public function setPickupTimeFrom(string $pickupTimeFrom): void;

    public function getPickupTimeTo(): string;

    public function setPickupTimeTo(string $pickupTimeTo): void;

    public function getPackage(): Package;

    public function setPackage(Package $package): void;
}
