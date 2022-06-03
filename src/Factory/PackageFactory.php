<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use BitBag\ShopwareDpdApp\Resolver\OrderCustomFieldsResolverInterface;
use T3ko\Dpd\Objects\Enum\Currency;
use T3ko\Dpd\Objects\Package;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

final class PackageFactory implements PackageFactoryInterface
{
    public const CASH_PAYMENT_CLASS = 'Shopware\Core\Checkout\Payment\Cart\PaymentHandler\CashPayment';

    private DpdSenderFactoryInterface $dpdSenderFactory;

    private OrderCustomFieldsResolverInterface $orderCustomFieldsResolver;

    private ParcelFactoryInterface $parcelFactory;

    private ReceiverFactoryInterface $receiverFactory;

    public function __construct(
        DpdSenderFactoryInterface $dpdSenderFactory,
        OrderCustomFieldsResolverInterface $orderCustomFieldsResolver,
        ParcelFactoryInterface $parcelFactory,
        ReceiverFactoryInterface $receiverFactory
    ) {
        $this->dpdSenderFactory = $dpdSenderFactory;
        $this->orderCustomFieldsResolver = $orderCustomFieldsResolver;
        $this->parcelFactory = $parcelFactory;
        $this->receiverFactory = $receiverFactory;
    }

    public function create(string $shopId, OrderEntity $order, Context $context): Package
    {
        $sender = $this->dpdSenderFactory->create($shopId);
        $orderAddress = $order->addresses?->first();
        $receiver = $this->receiverFactory->create($orderAddress);
        $parcel = $this->parcelFactory->create($order, $context);
        $package = new Package($sender, $receiver, [$parcel]);
        $package->addDeclaredValueService($order->amountTotal, Currency::PLN());

        $orderPaymentMethodHandlerIdentifier = $order->transactions?->first()?->paymentMethod?->handlerIdentifier;

        if (self::CASH_PAYMENT_CLASS === $orderPaymentMethodHandlerIdentifier) {
            $package->addCODService((float) $order->amountTotal, Currency::PLN());
        }

        $orderCustomFieldsResolver = $this->orderCustomFieldsResolver->resolve($order);

        $insurance = $orderCustomFieldsResolver['insurance'];

//        if (null !== $insurance) {
//            $package->addDeclaredValueService($insurance, Currency::PLN());
//        }

        return $package;
    }
}
