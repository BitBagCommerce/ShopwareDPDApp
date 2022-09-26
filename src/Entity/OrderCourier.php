<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Entity;

/** @psalm-suppress MissingConstructor */
class OrderCourier implements OrderCourierInterface
{
    private int $id;

    private string $pickupDate;

    private string $pickupTimeFrom;

    private string $pickupTimeTo;

    private Package $package;

    public function getId(): int
    {
        return $this->id;
    }

    public function getPickupDate(): string
    {
        return $this->pickupDate;
    }

    public function setPickupDate(string $pickupDate): void
    {
        $this->pickupDate = $pickupDate;
    }

    public function getPickupTimeFrom(): string
    {
        return $this->pickupTimeFrom;
    }

    public function setPickupTimeFrom(string $pickupTimeFrom): void
    {
        $this->pickupTimeFrom = $pickupTimeFrom;
    }

    public function getPickupTimeTo(): string
    {
        return $this->pickupTimeTo;
    }

    public function setPickupTimeTo(string $pickupTimeTo): void
    {
        $this->pickupTimeTo = $pickupTimeTo;
    }

    public function getPackage(): Package
    {
        return $this->package;
    }

    public function setPackage(Package $package): void
    {
        $this->package = $package;
    }
}
