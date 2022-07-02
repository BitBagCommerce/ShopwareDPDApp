<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory\OrderCourier;

use BitBag\ShopwareDpdApp\Entity\OrderCourier;
use BitBag\ShopwareDpdApp\Exception\Order\OrderException;
use BitBag\ShopwareDpdApp\Repository\ConfigRepositoryInterface;
use T3ko\Dpd\Api;
use T3ko\Dpd\Soap\Types\PackagesPickupCallV2Request;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

final class PackagePickupFactory implements PackagePickupFactoryInterface
{
    private PickupPayerFactoryInterface $pickupPayerFactory;

    private PickupCustomerFactoryInterface $pickupCustomerFactory;

    private PickupSenderFactoryInterface $pickupSenderFactory;

    private PickupParametersFactoryInterface $pickupParametersFactory;

    private PickupDetailsFactoryInterface $pickupDetailsFactory;

    private DpdPickupParametersFactoryInterface $dpdPickupParametersFactory;

    private ConfigRepositoryInterface $configRepository;

    public function __construct(
        PickupPayerFactoryInterface $pickupPayerFactory,
        PickupCustomerFactoryInterface $pickupCustomerFactory,
        PickupSenderFactoryInterface $pickupSenderFactory,
        PickupParametersFactoryInterface $pickupParametersFactory,
        PickupDetailsFactoryInterface $pickupDetailsFactory,
        DpdPickupParametersFactoryInterface $dpdPickupParametersFactory,
        ConfigRepositoryInterface $configRepository
    ) {
        $this->pickupPayerFactory = $pickupPayerFactory;
        $this->pickupCustomerFactory = $pickupCustomerFactory;
        $this->pickupSenderFactory = $pickupSenderFactory;
        $this->pickupParametersFactory = $pickupParametersFactory;
        $this->pickupDetailsFactory = $pickupDetailsFactory;
        $this->dpdPickupParametersFactory = $dpdPickupParametersFactory;
        $this->configRepository = $configRepository;
    }

    public function create(
        string $shopId,
        OrderEntity $order,
        OrderCourier $orderCourier,
        Api $api,
        Context $context
    ): PackagesPickupCallV2Request {
        $config = $this->configRepository->getByShopId($shopId);

        $pickupPayer = $this->pickupPayerFactory->create($api->getMasterFid(), $config);

        $billingAddress = $order->billingAddress;

        if (null === $billingAddress) {
            throw new OrderException('bitbag.shopware_dpd_app.order_courier.billing_address_not_found');
        }

        $pickupCustomer = $this->pickupCustomerFactory->create($billingAddress);
        $pickupSender = $this->pickupSenderFactory->create($config);
        $pickupParameters = $this->pickupParametersFactory->create($order, $context);
        $pickupDetails = $this->pickupDetailsFactory->create(
            $pickupPayer,
            $pickupCustomer,
            $pickupSender,
            $pickupParameters
        );
        $dpdPickupParameters = $this->dpdPickupParametersFactory->create($orderCourier, $pickupDetails);

        $request = new PackagesPickupCallV2Request();
        $request->setDpdPickupParams($dpdPickupParameters);

        return $request;
    }
}
