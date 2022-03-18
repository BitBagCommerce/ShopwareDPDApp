<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

interface CreateCustomFieldSetFactoryInterface
{
    public function create(string $name, string $labelName, string $entityName): array;
}
