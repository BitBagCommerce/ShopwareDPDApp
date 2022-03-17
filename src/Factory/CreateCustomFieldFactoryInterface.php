<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use BitBag\ShopwareDpdApp\AppSystem\Client\ClientInterface;

interface CreateCustomFieldFactoryInterface
{
    public function create(
        string $name,
        string $type,
        int $position,
        string $label,
        ClientInterface $client,
        ?string $customFieldSetId = null,
        ?array $customFieldSet = null
    ): void;
}
