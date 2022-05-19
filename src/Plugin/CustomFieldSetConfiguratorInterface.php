<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Plugin;

use Vin\ShopwareSdk\Data\Context;

interface CustomFieldSetConfiguratorInterface
{
    public function createCustomFieldSetForPackageDetails(Context $context): void;
}
