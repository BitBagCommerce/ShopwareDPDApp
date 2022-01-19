<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\AppSystem\Client;

use BitBag\ShopwareAppSkeleton\AppSystem\Credentials\CredentialsInterface;
use BitBag\ShopwareAppSkeleton\AppSystem\Exception\ClientBuilderException;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\HandlerStack;

final class ClientBuilder implements ClientBuilderInterface
{
    private CredentialsInterface $credentials;

    private array $headers = [];

    private ?HandlerStack $handlerStack;

    private ?HandlerStack $authenticationHandlerStack;

    public function __construct(
        CredentialsInterface $credentials,
        HandlerStack $handlerStack = null,
        HandlerStack $authenticationHandlerStack = null
    ) {
        $this->credentials = $credentials;
        $this->handlerStack = $handlerStack;
        $this->authenticationHandlerStack = $authenticationHandlerStack;
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
        $this->headers = array_merge($this->headers, $header);

        return new self(
            $this->credentials,
            $this->handlerStack,
            $this->authenticationHandlerStack
        );
    }

    public function withHandlerStack(HandlerStack $handlerStack): self
    {
        return new self(
            $this->credentials,
            $handlerStack,
            $this->authenticationHandlerStack
        );
    }

    public function withAuthenticationHandlerStack(HandlerStack $authenticationHandlerStack): self
    {
        return new self(
            $this->credentials,
            $this->handlerStack,
            $authenticationHandlerStack
        );
    }

    public function buildClient(): ClientInterface
    {
        $client = $this->getHttpClient();

        return new Client($client, $this->credentials->getShopUrl());
    }

    private function getHttpClient(): HttpClient
    {
        $token = $this->credentials->getToken();

        if (null !== $token) {
            return $this->getGuzzleClient($token);
        }

        throw new ClientBuilderException('Empty token.');
    }

    private function getGuzzleClient(string $token): HttpClient
    {
        $baseHeaders = [
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        return new HttpClient([
            'base_uri' => $this->credentials->getShopUrl(),
            'headers' => array_merge($baseHeaders, $this->headers),
            'handler' => $this->handlerStack,
        ]);
    }
}
