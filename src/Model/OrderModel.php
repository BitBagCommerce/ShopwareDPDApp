<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Model;

use Symfony\Component\Validator\Constraints as Assert;

final class OrderModel implements OrderModelInterface
{
    /**
     * @Assert\NotBlank(message="bitbag.shopware_dpd_app.validator.order_model.order_id")
     */
    private string $orderId;

    /**
     * @Assert\NotBlank(message="bitbag.shopware_dpd_app.validator.order_model.shop_id")
     */
    private string $shopId;

    /**
     * @Assert\NotBlank(message="bitbag.shopware_dpd_app.validator.order_model.email")
     */
    private string $email;

    /**
     * @Assert\NotBlank(message="bitbag.shopware_dpd_app.validator.order_model.weight")
     * @Assert\GreaterThan(value="0.0", message="bitbag.shopware_dpd_app.validator.order_model.weight")
     */
    private float $weight;

    /**
     * @Assert\Valid()
     */
    private PackageModelInterface $package;

    /**
     * @Assert\Valid()
     */
    private ShippingAddressModelInterface $shippingAddress;

    public function __construct(
        ?string $orderId,
        ?string $shopId,
        ?string $email,
        ?float $weight,
        ?PackageModelInterface $package,
        ?ShippingAddressModelInterface $shippingAddress
    ) {
        $this->orderId = $orderId;
        $this->shopId = $shopId;
        $this->email = $email;
        $this->weight = $weight;
        $this->package = $package;
        $this->shippingAddress = $shippingAddress;
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function getShopId(): string
    {
        return $this->shopId;
    }

    public function getPackage(): PackageModelInterface
    {
        return $this->package;
    }

    public function getShippingAddress(): ShippingAddressModelInterface
    {
        return $this->shippingAddress;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }
}
