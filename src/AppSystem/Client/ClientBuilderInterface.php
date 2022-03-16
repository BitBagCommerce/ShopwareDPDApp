<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\AppSystem\Client;

interface ClientBuilderInterface
{
    public function withLanguage(string $languageId): ClientBuilderInterface;

    public function withInheritance(bool $inheritance): ClientBuilderInterface;

    public function withHeader(array $header): ClientBuilderInterface;

    public function buildClient(): ClientInterface;
}
