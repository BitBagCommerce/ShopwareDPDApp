<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Validator;

use BitBag\ShopwareDpdApp\Exception\ErrorNotificationException;
use BitBag\ShopwareDpdApp\Model\ShippingAddressModel;
use BitBag\ShopwareDpdApp\Model\ShippingAddressModelInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ShippingAddressModelValidator implements ShippingAddressModelValidatorInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(?array $orderShippingAddress, ?string $currencyCode): ShippingAddressModelInterface
    {
        $shippingAddressModel = new ShippingAddressModel(
            $orderShippingAddress['firstName'] ?? null,
            $orderShippingAddress['lastName'] ?? null,
            $orderShippingAddress['street'] ?? null,
            $orderShippingAddress['zipcode'] ?? null,
            $orderShippingAddress['city'] ?? null,
            $currencyCode ?? null,
            $orderShippingAddress['phoneNumber'] ?? null,
        );

        $shippingAddressModelErrors = $this->validator->validate($shippingAddressModel);
        if (0 !== $shippingAddressModelErrors->count()) {
            throw new ErrorNotificationException($shippingAddressModelErrors[0]->getMessage());
        }

        return $shippingAddressModel;
    }
}
