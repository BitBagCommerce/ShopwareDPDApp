<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Plugin;

use BitBag\ShopwareDpdApp\Factory\CustomFieldsForPackageDetailsPayloadFactoryInterface;
use BitBag\ShopwareDpdApp\Filter\CustomFieldSetForPackageDetailsFilter;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Criteria;
use Vin\ShopwareSdk\Repository\RepositoryInterface;

final class CustomFieldSetConfigurator implements CustomFieldSetConfiguratorInterface
{
    private RepositoryInterface $customFieldSetRepository;

    private CustomFieldsForPackageDetailsPayloadFactoryInterface $customFieldsForPackageDetailsPayloadFactory;

    public function __construct(
        RepositoryInterface $customFieldSetRepository,
        CustomFieldsForPackageDetailsPayloadFactoryInterface $customFieldsForPackageDetailsPayloadFactory
    ) {
        $this->customFieldSetRepository = $customFieldSetRepository;
        $this->customFieldsForPackageDetailsPayloadFactory = $customFieldsForPackageDetailsPayloadFactory;
    }

    public function createCustomFieldSetForPackageDetails(Context $context): void
    {
        $customFieldSetCriteria = (new Criteria())->addFilter(new CustomFieldSetForPackageDetailsFilter());

        $customFieldSet = $this->customFieldSetRepository->searchIds($customFieldSetCriteria, $context);

        if (0 !== $customFieldSet->getTotal()) {
            return;
        }

        $data = $this->customFieldsForPackageDetailsPayloadFactory->create();

        $this->customFieldSetRepository->create($data, $context);
    }
}
