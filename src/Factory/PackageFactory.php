<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use BitBag\ShopwareDpdApp\Finder\OrderFinderInterface;
use T3ko\Dpd\Objects\Enum\Currency;
use T3ko\Dpd\Objects\Package;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

final class PackageFactory implements PackageFactoryInterface
{
    public const CASH_PAYMENT_CLASS = 'Shopware\Core\Checkout\Payment\Cart\PaymentHandler\CashPayment';

    private DpdSenderFactoryInterface $dpdSenderFactory;

    private ParcelFactoryInterface $parcelFactory;

    private ReceiverFactoryInterface $receiverFactory;

    private OrderFinderInterface $orderFinder;

    public function __construct(
        DpdSenderFactoryInterface $dpdSenderFactory,
        ParcelFactoryInterface $parcelFactory,
        ReceiverFactoryInterface $receiverFactory,
        OrderFinderInterface $orderFinder
    ) {
        $this->dpdSenderFactory = $dpdSenderFactory;
        $this->parcelFactory = $parcelFactory;
        $this->receiverFactory = $receiverFactory;
        $this->orderFinder = $orderFinder;
    }

    public function create(
        string $shopId,
        OrderEntity $order,
        Context $context
    ): Package {
        $salesChannelId = $this->orderFinder->getSalesChannelId($context, $order->id, $order);
        $sender = $this->dpdSenderFactory->create($shopId, $salesChannelId);
        $orderAddress = $order->addresses?->first();
        $receiver = $this->receiverFactory->create($orderAddress);
        $parcel = $this->parcelFactory->create($order, $context);
        $package = new Package($sender, $receiver, [$parcel]);

        $orderAmount = $order->amountTotal;

        if (null !== $orderAmount) {
            $package->addDeclaredValueService($orderAmount, Currency::PLN());

            $orderPaymentMethodHandlerIdentifier = $order->transactions?->first()?->paymentMethod?->handlerIdentifier;

            if (self::CASH_PAYMENT_CLASS === $orderPaymentMethodHandlerIdentifier) {
                $package->addCODService($orderAmount, Currency::PLN());
            }
        }

        return $package;
    }
}
