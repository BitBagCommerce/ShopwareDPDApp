<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Entity;

interface OrderInterface
{
    public function getId(): ?int;

    public function getShopId(): string;

    public function setShopId(string $shopId): void;

    public function getOrderId(): string;

    public function setOrderId(string $orderId): void;

    public function getParcelId(): int;

    public function setParcelId(int $parcelId): void;
}
