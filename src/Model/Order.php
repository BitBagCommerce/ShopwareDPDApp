<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Model;

class Order implements OrderInterface
{
    private array $order;

    private ?string $orderId;

    private string $shopId;

    protected Package $package;

    protected ShippingAddress $shippingAddress;

    private ?float $weight;

    public function __construct(array $order, string $shopId)
    {
        $this->order = $order;
        $this->shopId = $shopId;
        $this->orderId = $order['orderCustomer']['orderId'] ?? null;
        $this->package = new Package($order['customFields'] ?? []);
        $this->shippingAddress = new ShippingAddress(
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

    public function getPackage(): Package
    {
        return $this->package;
    }

    public function getShippingAddress(): ShippingAddress
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
