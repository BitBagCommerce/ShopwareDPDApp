<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Validator;

use BitBag\ShopwareDpdApp\Model\ShippingAddressModelInterface;

interface ShippingAddressModelValidatorInterface
{
    public function validate(?array $orderShippingAddress, ?string $currencyCode): ShippingAddressModelInterface;
}
