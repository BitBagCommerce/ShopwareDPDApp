<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Finder;

use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Criteria;
use Vin\ShopwareSdk\Data\Filter\EqualsFilter;
use Vin\ShopwareSdk\Repository\RepositoryInterface;
use Vin\ShopwareSdk\Repository\Struct\IdSearchResult;

final class PaymentMethodFinder implements PaymentMethodFinderInterface
{
    private RepositoryInterface $paymentMethodRepository;

    public function __construct(RepositoryInterface $paymentMethodRepository)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    public function find(string $value, Context $context): IdSearchResult
    {
        $criteria = (new Criteria())->addFilter(new EqualsFilter('handlerIdentifier', $value));

        return $this->paymentMethodRepository->searchIds($criteria, $context);
    }
}
