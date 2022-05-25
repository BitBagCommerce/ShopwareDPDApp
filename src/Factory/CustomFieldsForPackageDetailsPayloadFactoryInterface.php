<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

interface CustomFieldsForPackageDetailsPayloadFactoryInterface
{
    public const PACKAGE_DETAILS_KEY = 'bitbag_inpost_point_package_details';

    public function create(): array;
}
