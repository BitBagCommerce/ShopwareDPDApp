<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Entity;

use BitBag\ShopwareAppSystemBundle\Entity\ShopInterface;
use BitBag\ShopwareDpdApp\Repository\ConfigRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ConfigRepository::class)
 * @ORM\Table(name="config")
 * @psalm-suppress MissingConstructor
 */
class Config implements ConfigInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /** @ORM\Column(type="string", nullable=true) */
    protected string $apiLogin;

    /** @ORM\Column(type="string", nullable=true) */
    protected string $apiPassword;

    /** @ORM\Column(type="string", nullable=true) */
    protected string $apiFid;

    /** @ORM\Column(type="string", nullable=true) */
    protected string $apiEnvironment;

    /** @ORM\Column(type="string", nullable=true) */
    protected string $senderFirstLastName;

    /** @ORM\Column(type="string", nullable=true) */
    protected string $senderStreet;

    /** @ORM\Column(type="string", nullable=true) */
    protected string $senderZipCode;

    /** @ORM\Column(type="string", nullable=true) */
    protected string $senderCity;

    /** @ORM\Column(type="string", nullable=true) */
    protected string $senderPhoneNumber;

    /** @ORM\Column(type="string", nullable=true) */
    protected string $senderLocale;

    /**
     * @ORM\OneToOne(targetEntity="BitBag\ShopwareAppSystemBundle\Entity\Shop", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false, referencedColumnName="shop_id", onDelete="CASCADE")
     */
    protected ShopInterface $shop;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getApiLogin(): string
    {
        return $this->apiLogin;
    }

    public function setApiLogin(string $apiLogin): void
    {
        $this->apiLogin = $apiLogin;
    }

    public function getApiPassword(): string
    {
        return $this->apiPassword;
    }

    public function setApiPassword(string $apiPassword): void
    {
        $this->apiPassword = $apiPassword;
    }

    public function getApiFid(): int
    {
        return (int) $this->apiFid;
    }

    public function setApiFid(string $apiFid): void
    {
        $this->apiFid = $apiFid;
    }

    public function getApiEnvironment(): string
    {
        return $this->apiEnvironment;
    }

    public function setApiEnvironment(string $apiEnvironment): void
    {
        $this->apiEnvironment = $apiEnvironment;
    }

    public function getSenderFirstLastName(): string
    {
        return $this->senderFirstLastName;
    }

    public function setSenderFirstLastName(string $senderFirstLastName): void
    {
        $this->senderFirstLastName = $senderFirstLastName;
    }

    public function getSenderStreet(): string
    {
        return $this->senderStreet;
    }

    public function setSenderStreet(string $senderStreet): void
    {
        $this->senderStreet = $senderStreet;
    }

    public function getSenderZipCode(): string
    {
        return $this->senderZipCode;
    }

    public function setSenderZipCode(string $senderZipCode): void
    {
        $zipCode = str_replace(
            [
                '-',
                ' ',
            ],
            '',
            $senderZipCode
        );

        $this->senderZipCode = $zipCode;
    }

    public function getSenderCity(): string
    {
        return $this->senderCity;
    }

    public function setSenderCity(string $senderCity): void
    {
        $this->senderCity = $senderCity;
    }

    public function getSenderPhoneNumber(): string
    {
        return $this->senderPhoneNumber;
    }

    public function setSenderPhoneNumber(string $senderPhoneNumber): void
    {
        $this->senderPhoneNumber = $senderPhoneNumber;
    }

    public function getSenderLocale(): string
    {
        return $this->senderLocale;
    }

    public function setSenderLocale(string $senderLocale): void
    {
        $this->senderLocale = strtoupper($senderLocale);
    }

    public function getShop(): ShopInterface
    {
        return $this->shop;
    }

    public function setShop(ShopInterface $shop): void
    {
        $this->shop = $shop;
    }
}
