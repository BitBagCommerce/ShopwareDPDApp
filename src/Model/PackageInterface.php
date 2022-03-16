<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Model;

interface PackageInterface
{
    public function getHeight(): ?int;

    public function getWidth(): ?int;

    public function getDepth(): ?int;

    public function getDescription(): ?string;
}
