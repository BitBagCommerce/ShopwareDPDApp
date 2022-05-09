<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\AppSystem\Credentials;

interface OAuthCredentialsInterface
{
    public function getTokenType(): string;

    public function getAccessToken(): string;

    public function isExpired(): bool;
}
