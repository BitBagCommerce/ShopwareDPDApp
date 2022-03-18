<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Validator;

use BitBag\ShopwareDpdApp\Exception\ErrorNotificationException;
use BitBag\ShopwareDpdApp\Model\OrderModel;
use BitBag\ShopwareDpdApp\Model\OrderModelInterface;
use BitBag\ShopwareDpdApp\Model\PackageModelInterface;
use BitBag\ShopwareDpdApp\Model\ShippingAddressModelInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class OrderModelValidator implements OrderModelValidatorInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(
        ?string $orderId,
        ?string $shopId,
        ?string $email,
        ?float $weight,
        ?PackageModelInterface $packageModel,
        ?ShippingAddressModelInterface $shippingAddressModel
    ): OrderModelInterface {
        $orderModel = new OrderModel(
            $orderId,
            $shopId,
            $email,
            $weight,
            $packageModel,
            $shippingAddressModel
        );
        $orderModelErrors = $this->validator->validate($orderModel);
        if (0 !== $orderModelErrors->count()) {
            throw new ErrorNotificationException($orderModelErrors[0]->getMessage());
        }

        return $orderModel;
    }
}
