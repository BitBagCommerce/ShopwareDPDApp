<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\AppSystem\Credentials;

interface CredentialsInterface
{
    public function getShopUrl(): string;

    public function getApiKey(): string;

    public function getSecretKey(): string;

    public function getToken(): ?string;

    public function withToken(string $token): self;
}
