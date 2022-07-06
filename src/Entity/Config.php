<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Entity;

use BitBag\ShopwareAppSystemBundle\Entity\ShopInterface;

/**
 * @psalm-suppress MissingConstructor
 */
class Config implements ConfigInterface
{
    protected ?int $id;

    protected string $apiLogin;

    protected string $apiPassword;

    protected string $apiFid;

    protected string $apiEnvironment;

    protected string $senderFirstLastName;

    protected string $senderStreet;

    protected string $senderZipCode;

    protected string $senderCity;

    protected string $senderPhoneNumber;

    protected string $senderLocale;

    protected ShopInterface $shop;

    protected string $salesChannelId;

    public function __clone()
    {
        $this->id = null;
    }

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

    public function getSalesChannelId(): string
    {
        return $this->salesChannelId;
    }

    public function setSalesChannelId(string $salesChannelId): void
    {
        $this->salesChannelId = $salesChannelId;
    }
}
