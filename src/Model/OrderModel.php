<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Model;

class OrderModel implements OrderModelInterface
{
    private array $order;

    private ?string $orderId;

    private string $shopId;

    protected PackageModel $package;

    protected ShippingAddressModel $shippingAddress;

    private ?float $weight;

    public function __construct(array $order, string $shopId)
    {
        $this->order = $order;
        $this->shopId = $shopId;
        $this->orderId = $order['orderCustomer']['orderId'] ?? null;
        $this->package = new PackageModel($order['customFields'] ?? []);
        $this->shippingAddress = new ShippingAddressModel(
            $order['deliveries'][0]['shippingOrderAddress'] ?? [],
            $order['customFields']['package_details_countryCode'] ?? null
        );
        $this->setWeight($order);
    }

    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    public function getShopId(): string
    {
        return $this->shopId;
    }

    public function getPackage(): PackageModel
    {
        return $this->package;
    }

    public function getShippingAddress(): ShippingAddressModel
    {
        return $this->shippingAddress;
    }

    public function getEmail(): ?string
    {
        return $this->order['orderCustomer']['email'] ?? null;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    private function setWeight(array $orderData): void
    {
        $lineItems = $orderData['lineItems'];

        $totalWeight = 0;

        foreach ($lineItems as $item) {
            $weight = $item['quantity'] * $item['product']['weight'];
            $totalWeight += $weight;
        }

        $this->weight = $totalWeight;
    }
}
