<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Validator;

use BitBag\ShopwareDpdApp\Model\OrderModelInterface;
use BitBag\ShopwareDpdApp\Model\PackageModelInterface;
use BitBag\ShopwareDpdApp\Model\ShippingAddressModelInterface;

interface OrderModelValidatorInterface
{
    public function validate(
        ?string $orderId,
        ?string $shopId,
        ?string $email,
        ?float $weight,
        ?PackageModelInterface $packageModel,
        ?ShippingAddressModelInterface $shippingAddressModel
    ): OrderModelInterface;
}
