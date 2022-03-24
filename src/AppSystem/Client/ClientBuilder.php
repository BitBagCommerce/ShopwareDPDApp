<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\AppSystem\Client;

use BitBag\ShopwareAppSkeleton\AppSystem\Authenticator\OAuthAuthenticatorInterface;
use BitBag\ShopwareAppSkeleton\AppSystem\Credentials\OAuthCredentialsInterface;
use BitBag\ShopwareAppSkeleton\Entity\ShopInterface;
use GuzzleHttp\Client as HttpClient;

final class ClientBuilder implements ClientBuilderInterface
{
    private ShopInterface $shop;

    private OAuthAuthenticatorInterface $authenticator;

    private array $headers;

    private ?OAuthCredentialsInterface $oauthCredentials = null;

    public function __construct(
        ShopInterface $shop,
        OAuthAuthenticatorInterface $authenticator,
        array $headers = []
    ) {
        $this->shop = $shop;
        $this->authenticator = $authenticator;
        $this->headers = $headers;
    }

    public function withLanguage(string $languageId): self
    {
        return $this->withHeader(['languageId' => $languageId]);
    }

    public function withInheritance(bool $inheritance): self
    {
        return $this->withHeader(['inheritance' => $inheritance]);
    }

    public function withHeader(array $header): self
    {
        $headers = array_merge($this->headers, $header);

        return new self(
            $this->shop,
            $this->authenticator,
            $headers
        );
    }

    public function buildClient(): ClientInterface
    {
        $client = $this->getHttpClient();

        return new Client($client, $this->shop->getShopUrl());
    }

    private function getHttpClient(): HttpClient
    {
        if (null === $this->oauthCredentials || $this->oauthCredentials->isExpired()) {
            $this->oauthCredentials = $this->authenticator->authenticate($this->shop);
        }

        return $this->getGuzzleClient($this->oauthCredentials->getAccessToken());
    }

    private function getGuzzleClient(string $accessToken): HttpClient
    {
        $baseHeaders = [
            'Authorization' => 'Bearer ' . $accessToken,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        return new HttpClient([
            'base_uri' => $this->shop->getShopUrl(),
            'headers' => array_merge($baseHeaders, $this->headers),
        ]);
    }
}
