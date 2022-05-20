<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Finder;

use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Criteria;
use Vin\ShopwareSdk\Data\Filter\ContainsFilter;
use Vin\ShopwareSdk\Data\Filter\EqualsFilter;
use Vin\ShopwareSdk\Repository\RepositoryInterface;
use Vin\ShopwareSdk\Repository\Struct\IdSearchResult;

final class DeliveryTimeFinder implements DeliveryTimeFinderInterface
{
    private RepositoryInterface $deliveryTimeRepository;

    public function __construct(RepositoryInterface $deliveryTimeRepository)
    {
        $this->deliveryTimeRepository = $deliveryTimeRepository;
    }

    public function findDeliveryTimeByMinMax(int $min, int $max, Context $context): IdSearchResult
    {
        $criteria = (new Criteria())
            ->addFilter(new ContainsFilter('unit', 'day'))
            ->addFilter(new EqualsFilter('min', $min))
            ->addFilter(new EqualsFilter('max', $max));

        return $this->deliveryTimeRepository->searchIds($criteria, $context);
    }
}
