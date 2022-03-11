<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\Model;

interface ModelValidInterface
{
    public function isValid(): bool;
}
