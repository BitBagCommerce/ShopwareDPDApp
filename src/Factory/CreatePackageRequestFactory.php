<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use BitBag\ShopwareDpdApp\Api\ApiService;
use BitBag\ShopwareDpdApp\Calculator\OrderWeightCalculatorInterface;
use BitBag\ShopwareDpdApp\Exception\Order\OrderException;
use BitBag\ShopwareDpdApp\Provider\Defaults;
use BitBag\ShopwareDpdApp\Resolver\OrderCustomFieldsResolverInterface;
use T3ko\Dpd\Objects\Enum\Currency;
use T3ko\Dpd\Objects\Package;
use T3ko\Dpd\Objects\Parcel;
use T3ko\Dpd\Objects\Receiver;
use T3ko\Dpd\Request\GeneratePackageNumbersRequest;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;
use Vin\ShopwareSdk\Data\Entity\OrderAddress\OrderAddressEntity;

final class CreatePackageRequestFactory implements CreatePackageRequestFactoryInterface
{
    private ApiService $apiService;

    private CreateDpdSenderFactoryInterface $createDpdSender;

    private OrderCustomFieldsResolverInterface $orderCustomFieldsResolver;

    private OrderWeightCalculatorInterface $orderWeightCalculator;

    public function __construct(
        ApiService $apiService,
        CreateDpdSenderFactoryInterface $createDpdSender,
        OrderCustomFieldsResolverInterface $orderCustomFieldsResolver,
        OrderWeightCalculatorInterface $orderWeightCalculator
    ) {
        $this->apiService = $apiService;
        $this->createDpdSender = $createDpdSender;
        $this->orderCustomFieldsResolver = $orderCustomFieldsResolver;
        $this->orderWeightCalculator = $orderWeightCalculator;
    }

    public function create(string $shopId, OrderEntity $order, Context $context): GeneratePackageNumbersRequest
    {
        $sender = $this->createDpdSender->create($shopId);

        $customFields = $order->getCustomFields();

        $firstAddress = $order->addresses?->first();

        if (null === $firstAddress) {
            throw new OrderException('bitbag.shopware_dpd_app.order.shippingAddressNotFound');
        }

        $receiver = $this->createReceiver(
            $firstAddress,
            $customFields['package_details_countryCode'] ?? Defaults::CURRENCY_CODE
        );

        $orderCustomFieldsResolver = $this->orderCustomFieldsResolver->resolve($order);

        $parcel = $this->createParcel($order, $orderCustomFieldsResolver, $context);

        $package = new Package($sender, $receiver, [$parcel]);

        $orderAmount = $order->amountTotal;

        if (null === $orderAmount || 0.0 === $orderAmount) {
            throw new OrderException('bitbag.shopware_dpd_app.order.nullAmount');
        }

        $orderPaymentMethodHandlerIdentifier = $order->transactions?->first()?->paymentMethod?->handlerIdentifier;

        if ('Shopware\Core\Checkout\Payment\Cart\PaymentHandler\CashPayment' === $orderPaymentMethodHandlerIdentifier) {
            $package->addCODService($orderAmount, Currency::PLN());
        }

        if ($orderCustomFieldsResolver['insurance']) {
            $package->addDeclaredValueService($orderCustomFieldsResolver['insurance'], Currency::PLN());
        }

        return GeneratePackageNumbersRequest::fromPackage($package);
    }

    private function createReceiver(OrderAddressEntity $address, string $currencyCode): Receiver
    {
        $phoneNumber = $address->phoneNumber;
        $firstName = $address->firstName;
        $lastName = $address->lastName;
        $street = $address->street;
        $zipcode = $address->zipcode;
        $city = $address->city;

        if (null === $phoneNumber ||
            null === $firstName ||
            null === $lastName ||
            null == $street ||
            null === $zipcode ||
            null === $city
        ) {
            throw new OrderException('bitbag.shopware_dpd_app.order.shippingAddressNotFound');
        }

        return new Receiver(
            $phoneNumber,
            $firstName . ' ' . $lastName,
            $street,
            $zipcode,
            $city,
            $currencyCode
        );
    }

    private function createParcel(OrderEntity $order, array $orderCustomFieldsResolver, Context $context): Parcel
    {
        $weight = $this->orderWeightCalculator->calculate($order, $context);

        return new Parcel(
            $orderCustomFieldsResolver['width'],
            $orderCustomFieldsResolver['height'],
            $orderCustomFieldsResolver['depth'],
            $weight
        );
    }
}
