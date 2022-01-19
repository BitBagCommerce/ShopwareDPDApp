<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\AppSystem\Client;

use GuzzleHttp\HandlerStack;

interface ClientBuilderInterface
{
    public function withLanguage(string $languageId): ClientBuilderInterface;

    public function withInheritance(bool $inheritance): ClientBuilderInterface;

    public function withHeader(array $header): ClientBuilderInterface;

    public function withHandlerStack(HandlerStack $handlerStack): ClientBuilderInterface;

    public function withAuthenticationHandlerStack(HandlerStack $authenticationHandlerStack): ClientBuilderInterface;

    public function buildClient(): ClientInterface;
}
