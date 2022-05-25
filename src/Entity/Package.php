<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Entity;

use BitBag\ShopwareDpdApp\Repository\PackageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PackageRepository::class)
 * @ORM\Table(name="packages")
 */
class Package implements PackageInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /** @ORM\Column(type="string") */
    protected string $shopId;

    /** @ORM\Column(type="string") */
    protected string $orderId;

    /** @ORM\Column(type="integer") */
    protected int $parcelId;

    /** @ORM\Column(type="string") */
    protected string $waybill;

    public function __construct(string $shopId, string $orderId, int $parcelId, string $waybill)
    {
        $this->shopId = $shopId;
        $this->orderId = $orderId;
        $this->parcelId = $parcelId;
        $this->waybill = $waybill;
    }

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
}
