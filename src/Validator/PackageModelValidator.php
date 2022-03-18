<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Validator;

use BitBag\ShopwareDpdApp\Exception\ErrorNotificationException;
use BitBag\ShopwareDpdApp\Model\PackageModel;
use BitBag\ShopwareDpdApp\Model\PackageModelInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PackageModelValidator implements PackageModelValidatorInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(array $customFields): PackageModelInterface
    {
        $packageModel = new PackageModel(
            $customFields['package_details_height'] ?? null,
            $customFields['package_details_width'] ?? null,
            $customFields['package_details_depth'] ?? null
        );

        $packageModelErrors = $this->validator->validate($packageModel);
        if (0 !== $packageModelErrors->count()) {
            throw new ErrorNotificationException($packageModelErrors[0]->getMessage());
        }

        return $packageModel;
    }
}
