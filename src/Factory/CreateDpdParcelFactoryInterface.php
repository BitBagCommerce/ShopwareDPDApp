<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use BitBag\ShopwareDpdApp\Model\PackageModelInterface;
use T3ko\Dpd\Objects\Parcel;

interface CreateDpdParcelFactoryInterface
{
    public function create(PackageModelInterface $package, float $weight): Parcel;
}
