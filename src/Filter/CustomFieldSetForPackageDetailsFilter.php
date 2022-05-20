<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Filter;

use BitBag\ShopwareDpdApp\Factory\CustomFieldsForPackageDetailsPayloadFactoryInterface;
use Vin\ShopwareSdk\Data\Filter\EqualsFilter;

final class CustomFieldSetForPackageDetailsFilter extends EqualsFilter
{
    private const TECHNICAL_NAME = 'config.technical_name';

    public function __construct()
    {
        parent::__construct(
            self::TECHNICAL_NAME,
            CustomFieldsForPackageDetailsPayloadFactoryInterface::PACKAGE_DETAILS_KEY
        );
    }
}
