<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Entity;

class Package implements PackageInterface
{
    protected int $id;

    protected string $shopId;

    protected string $orderId;

    protected int $parcelId;

    protected string $waybill;

    protected ?string $orderCourierNumber;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShopId(): string
    {
        return $this->shopId;
    }

    public function setShopId(string $shopId): void
    {
        $this->shopId = $shopId;
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function setOrderId(string $orderId): void
    {
        $this->orderId = $orderId;
    }

    public function getParcelId(): int
    {
        return $this->parcelId;
    }

    public function setParcelId(int $parcelId): void
    {
        $this->parcelId = $parcelId;
    }

    public function getWaybill(): string
    {
        return $this->waybill;
    }

    public function setWaybill(string $waybill): void
    {
        $this->waybill = $waybill;
    }

    public function getOrderCourierNumber(): ?string
    {
        return $this->orderCourierNumber;
    }

    public function setOrderCourierNumber(?string $orderCourierNumber): void
    {
        $this->orderCourierNumber = $orderCourierNumber;
    }
}
