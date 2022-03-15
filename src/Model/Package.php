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
        return (int) ($this->shippingAddressData['package_details_height'] ?? null);
    }

    public function getWidth(): ?int
    {
        return (int) ($this->shippingAddressData['package_details_width'] ?? null);
    }

    public function getDepth(): ?int
    {
        return (int) ($this->shippingAddressData['package_details_depth'] ?? null);
    }

    public function getDescription(): ?string
    {
        return $this->shippingAddressData['package_details_description'] ?? null;
    }

    public function isValid(): bool
    {
        return $this->getHeight() &&
            $this->getWidth() &&
            $this->getDepth() &&
            $this->getDescription();
    }
}
