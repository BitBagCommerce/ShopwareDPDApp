<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use BitBag\ShopwareDpdApp\AppSystem\Client\ClientInterface;

interface CreateDetailsPackageFieldsFactoryInterface
{
    public function create(ClientInterface $client): void;
}
