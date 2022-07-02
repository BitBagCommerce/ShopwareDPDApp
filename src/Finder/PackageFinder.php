<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Finder;

use BitBag\ShopwareDpdApp\Repository\PackageRepositoryInterface;

final class PackageFinder implements PackageFinderInterface
{
    private PackageRepositoryInterface $packageRepository;

    public function __construct(PackageRepositoryInterface $packageRepository)
    {
        $this->packageRepository = $packageRepository;
    }

    public function findOrdersWithoutOrderCourier(): array
    {
        $packagesIds = $this->packageRepository->findOrdersWithoutOrderCourier();

        $packages = [];

        foreach ($packagesIds as $package) {
            $packages[$package->getId()] = $package;
        }

        return $packages;
    }
}
