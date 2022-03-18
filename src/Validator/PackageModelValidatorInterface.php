<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Validator;

use BitBag\ShopwareDpdApp\Model\PackageModelInterface;

interface PackageModelValidatorInterface
{
    public function validate(array $customFields): PackageModelInterface;
}
