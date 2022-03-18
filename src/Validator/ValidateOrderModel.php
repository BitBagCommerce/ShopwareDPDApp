<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Validator;

use BitBag\ShopwareDpdApp\Calculator\OrderWeightCalculatorInterface;
use BitBag\ShopwareDpdApp\Exception\ErrorNotificationException;
use BitBag\ShopwareDpdApp\Model\OrderModelInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ValidateOrderModel implements ValidateOrderModelInterface
{
    private ValidatorInterface $validator;

    private PackageModelValidatorInterface $validatePackageModel;

    private ShippingAddressModelValidatorInterface $validateShippingAddressModel;

    private OrderModelValidatorInterface $orderModelValidator;

    private OrderWeightCalculatorInterface $orderWeightCalculator;

    public function __construct(
        ValidatorInterface $validator,
        PackageModelValidatorInterface $validatePackageModel,
        ShippingAddressModelValidatorInterface $validateShippingAddressModel,
        OrderModelValidatorInterface $orderModelValidator,
        OrderWeightCalculatorInterface $orderWeightCalculator
    ) {
        $this->validator = $validator;
        $this->validatePackageModel = $validatePackageModel;
        $this->validateShippingAddressModel = $validateShippingAddressModel;
        $this->orderModelValidator = $orderModelValidator;
        $this->orderWeightCalculator = $orderWeightCalculator;
    }

    public function validate(array $order, string $orderId, string $shopId): OrderModelInterface
    {
        try {
            $packageModel = $this->validatePackageModel->validate($order['customFields'] ?? []);
        } catch (ErrorNotificationException $exception) {
            throw new ErrorNotificationException($exception->getMessage());
        }

        try {
            $orderShippingAddress = $order['deliveries'][0]['shippingOrderAddress'];

            $shippingAddressModel = $this->validateShippingAddressModel->validate(
                $orderShippingAddress,
                $order['customFields']['package_details_countryCode'] ?? null
            );
        } catch (ErrorNotificationException $exception) {
            throw new ErrorNotificationException($exception->getMessage());
        }

        $orderWeight = $this->orderWeightCalculator->calculate($order['lineItems']);

        try {
            $orderModel = $this->orderModelValidator->validate(
                $orderId,
                $shopId,
                $order['orderCustomer']['email'],
                $orderWeight,
                $packageModel,
                $shippingAddressModel
            );
        } catch (ErrorNotificationException $exception) {
            throw new ErrorNotificationException($exception->getMessage());
        }

        return $orderModel;
    }
}
