<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\Model;

class Package implements PackageInterface, ModelValidInterface
{
    private array $shippingAddressData;

    public function __construct(array $shippingAddressData)
    {
        $this->shippingAddressData = $shippingAddressData;
    }

    public function getHeight(): ?int
    {
        return (int) $this->shippingAddressData['dpd_package_height'] ?? null;
    }

    public function getWidth(): ?int
    {
        return (int) $this->shippingAddressData['dpd_package_width'] ?? null;
    }

    public function getDepth(): ?int
    {
        return (int) $this->shippingAddressData['dpd_package_depth'] ?? null;
    }

    public function getDescription(): ?string
    {
        return $this->shippingAddressData['dpd_package_description'] ?? null;
    }

    public function isValid(): bool
    {
        return $this->getHeight() &&
            $this->getWidth() &&
            $this->getDepth() &&
            $this->getDescription();
    }
}
