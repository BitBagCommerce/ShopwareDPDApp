<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\AppSystem\Credentials;

final class OAuthCredentials implements OAuthCredentialsInterface
{
    private const SECURITY_THRESHOLD = 10;

    private string $tokenType;

    private int $expiresIn;

    private string $accessToken;

    public function __construct(string $tokenType, int $expiresIn, string $accessToken)
    {
        $this->tokenType = $tokenType;
        $this->accessToken = $accessToken;
        $this->expiresIn = \strtotime("+$expiresIn seconds");
    }

    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function isExpired(): bool
    {
        return (\time() - self::SECURITY_THRESHOLD) >= $this->expiresIn;
    }
}
