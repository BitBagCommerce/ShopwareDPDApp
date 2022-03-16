<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Model;

interface ModelValidInterface
{
    public function isValid(): bool;
}
