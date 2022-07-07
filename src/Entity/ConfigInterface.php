<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Entity;

use BitBag\ShopwareAppSystemBundle\Entity\ShopInterface;

interface ConfigInterface
{
    public const SANDBOX_ENVIRONMENT = 'sandbox';

    public const PRODUCTION_ENVIRONMENT = 'production';

    public function getId(): ?int;

    public function setId(int $id): void;

    public function getApiLogin(): string;

    public function setApiLogin(string $apiLogin): void;

    public function getApiPassword(): string;

    public function setApiPassword(string $apiPassword): void;

    public function getApiFid(): int;

    public function setApiFid(string $apiFid): void;

    public function getApiEnvironment(): string;

    public function setApiEnvironment(string $apiEnvironment): void;

    public function getSenderFirstLastName(): string;

    public function setSenderFirstLastName(string $senderFirstLastName): void;

    public function getSenderStreet(): string;

    public function setSenderStreet(string $senderStreet): void;

    public function getSenderZipCode(): string;

    public function setSenderZipCode(string $senderZipCode): void;

    public function getSenderCity(): string;

    public function setSenderCity(string $senderCity): void;

    public function getSenderPhoneNumber(): string;

    public function setSenderPhoneNumber(string $senderPhoneNumber): void;

    public function getShop(): ShopInterface;

    public function setShop(ShopInterface $shop): void;
}
