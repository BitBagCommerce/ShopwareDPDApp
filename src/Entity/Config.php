<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\Entity;

use BitBag\ShopwareAppSkeleton\Repository\ConfigRepository;
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

    /**
     * @ORM\Column(type="string")
     */
    protected string $apiLogin;

    /**
     * @ORM\Column(type="string")
     */
    protected string $apiPassword;

    /**
     * @ORM\Column(type="string")
     */
    protected string $apiFid;

    /**
     * @ORM\Column(type="string")
     */
    protected string $senderFirstLastName;

    /**
     * @ORM\Column(type="string")
     */
    protected string $senderStreet;

    /**
     * @ORM\Column(type="string")
     */
    protected string $senderZipCode;

    /**
     * @ORM\Column(type="string")
     */
    protected string $senderCity;

    /**
     * @ORM\Column(type="string")
     */
    protected string $senderPhoneNumber;

    /**
     * @ORM\Column(type="string")
     */
    protected string $senderLocale;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getApiLogin(): ?string
    {
        return $this->apiLogin;
    }

    public function setApiLogin(string $apiLogin): void
    {
        $this->apiLogin = $apiLogin;
    }

    public function getApiPassword(): ?string
    {
        return $this->apiPassword;
    }

    public function setApiPassword(string $apiPassword): void
    {
        $this->apiPassword = $apiPassword;
    }

    public function getApiFid(): ?int
    {
        return (int) $this->apiFid;
    }

    public function setApiFid(string $apiFid): void
    {
        $this->apiFid = $apiFid;
    }

    public function getSenderFirstLastName(): ?string
    {
        return $this->senderFirstLastName;
    }

    public function setSenderFirstLastName(string $senderFirstLastName): void
    {
        $this->senderFirstLastName = $senderFirstLastName;
    }

    public function getSenderStreet(): ?string
    {
        return $this->senderStreet;
    }

    public function setSenderStreet(string $senderStreet): void
    {
        $this->senderStreet = $senderStreet;
    }

    public function getSenderZipCode(): ?string
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

    public function getSenderCity(): ?string
    {
        return $this->senderCity;
    }

    public function setSenderCity(string $senderCity): void
    {
        $this->senderCity = $senderCity;
    }

    public function getSenderPhoneNumber(): ?string
    {
        return $this->senderPhoneNumber;
    }

    public function setSenderPhoneNumber(string $senderPhoneNumber): void
    {
        $this->senderPhoneNumber = $senderPhoneNumber;
    }

    public function getSenderLocale(): ?string
    {
        return $this->senderLocale;
    }

    public function setSenderLocale(string $senderLocale): void
    {
        $this->senderLocale = $senderLocale;
    }
}
