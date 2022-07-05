<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Finder;

use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Criteria;
use Vin\ShopwareSdk\Data\Filter\EqualsFilter;
use Vin\ShopwareSdk\Repository\RepositoryInterface;
use Vin\ShopwareSdk\Repository\Struct\EntitySearchResult;

final class SalesChannelFinder implements SalesChannelFinderInterface
{
    private RepositoryInterface $salesChannelRepository;

    public function __construct(RepositoryInterface $salesChannelRepository)
    {
        $this->salesChannelRepository = $salesChannelRepository;
    }

    public function findAll(Context $context): EntitySearchResult
    {
        return $this->salesChannelRepository->search(new Criteria(), $context);
    }

    public function findById(string $id, Context $context): EntitySearchResult
    {
        $criteria = (new Criteria())->addFilter(new EqualsFilter('id', $id));

        return $this->salesChannelRepository->search($criteria, $context);
    }
}
