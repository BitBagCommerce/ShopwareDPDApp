<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Finder;

use BitBag\ShopwareDpdApp\Factory\ShippingMethodPayloadFactoryInterface;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Criteria;
use Vin\ShopwareSdk\Data\Filter\ContainsFilter;
use Vin\ShopwareSdk\Repository\RepositoryInterface;
use Vin\ShopwareSdk\Repository\Struct\IdSearchResult;

final class ShippingMethodFinder implements ShippingMethodFinderInterface
{
    private RepositoryInterface $shippingMethodRepository;

    public function __construct(RepositoryInterface $shippingMethodRepository)
    {
        $this->shippingMethodRepository = $shippingMethodRepository;
    }

    public function find(Context $context): IdSearchResult
    {
        $shippingKey = ShippingMethodPayloadFactoryInterface::SHIPPING_KEY;

        $shippingMethodCriteria = (new Criteria())->addFilter(new ContainsFilter('name', $shippingKey));

        return $this->shippingMethodRepository->searchIds($shippingMethodCriteria, $context);
    }
}
