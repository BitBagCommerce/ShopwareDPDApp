<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\Entity;

use BitBag\ShopwareAppSkeleton\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="orders")
 * @psalm-suppress MissingConstructor
 */
class Order implements OrderInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /**
     * @ORM\Column(type="string")
     */
    protected string $shopId;

    /**
     * @ORM\Column(type="string")
     */
    protected string $orderId;

    /**
     * @ORM\Column(type="integer")
     */
    protected int $parcelId;

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
}
