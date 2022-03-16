<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Model;

interface OrderInterface
{
    public function getOrderId(): ?string;

    public function getShopId(): string;

    public function getPackage(): Package;

    public function getShippingAddress(): ShippingAddress;

    public function getEmail(): ?string;
}
