<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Provider;

interface CustomFieldNamesProviderInterface
{
    public function getFields(): array;
}
