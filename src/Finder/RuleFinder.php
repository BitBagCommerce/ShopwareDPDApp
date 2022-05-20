<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Finder;

use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Criteria;
use Vin\ShopwareSdk\Data\Filter\EqualsFilter;
use Vin\ShopwareSdk\Repository\RepositoryInterface;
use Vin\ShopwareSdk\Repository\Struct\IdSearchResult;

final class RuleFinder implements RuleFinderInterface
{
    private RepositoryInterface $ruleRepository;

    public function __construct(RepositoryInterface $ruleRepository)
    {
        $this->ruleRepository = $ruleRepository;
    }

    public function find(string $name, Context $context): IdSearchResult
    {
        $criteria = (new Criteria())->addFilter(new EqualsFilter('name', $name));

        return $this->ruleRepository->searchIds($criteria, $context);
    }
}
