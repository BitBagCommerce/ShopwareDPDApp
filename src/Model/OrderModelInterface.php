<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Model;

interface OrderModelInterface
{
    public function getOrderId(): string;

    public function getShopId(): string;

    public function getPackage(): PackageModelInterface;

    public function getShippingAddress(): ShippingAddressModelInterface;

    public function getEmail(): string;
}
