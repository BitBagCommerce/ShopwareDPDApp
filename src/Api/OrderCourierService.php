<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Api;

use BitBag\ShopwareDpdApp\Entity\OrderCourier;
use BitBag\ShopwareDpdApp\Entity\Package;
use BitBag\ShopwareDpdApp\Factory\OrderCourier\DpdPickupParametersInterface;
use BitBag\ShopwareDpdApp\Factory\OrderCourier\PickupCustomerInterface;
use BitBag\ShopwareDpdApp\Factory\OrderCourier\PickupDetailsInterface;
use BitBag\ShopwareDpdApp\Factory\OrderCourier\PickupParametersInterface;
use BitBag\ShopwareDpdApp\Factory\OrderCourier\PickupPayerInterface;
use BitBag\ShopwareDpdApp\Factory\OrderCourier\PickupSenderInterface;
use BitBag\ShopwareDpdApp\Repository\ConfigRepositoryInterface;
use BitBag\ShopwareDpdApp\Resolver\ApiClientResolverInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use T3ko\Dpd\Soap\Types\PackagesPickupCallV2Request;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

final class OrderCourierService implements OrderCourierServiceInterface
{
    private PickupPayerInterface $pickupPayer;

    private PickupCustomerInterface $pickupCustomer;

    private PickupSenderInterface $pickupSender;

    private PickupParametersInterface $pickupParameters;

    private PickupDetailsInterface $pickupDetails;

    private DpdPickupParametersInterface $dpdPickupParameters;

    private EntityManagerInterface $entityManager;

    private TranslatorInterface $translator;

    private ApiClientResolverInterface $apiClientResolver;

    private ConfigRepositoryInterface $configRepository;

    public function __construct(
        PickupPayerInterface $pickupPayer,
        PickupCustomerInterface $pickupCustomer,
        PickupSenderInterface $pickupSender,
        PickupParametersInterface $pickupParameters,
        PickupDetailsInterface $pickupDetails,
        DpdPickupParametersInterface $dpdPickupParameters,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        ApiClientResolverInterface $apiClientResolver,
        ConfigRepositoryInterface $configRepository
    ) {
        $this->pickupPayer = $pickupPayer;
        $this->pickupCustomer = $pickupCustomer;
        $this->pickupSender = $pickupSender;
        $this->pickupParameters = $pickupParameters;
        $this->pickupDetails = $pickupDetails;
        $this->dpdPickupParameters = $dpdPickupParameters;
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->apiClientResolver = $apiClientResolver;
        $this->configRepository = $configRepository;
    }

    public function orderCourierByPackages(
        array $orders,
        array $packages,
        array $packagesBySelectedOrdersArr,
        string $shopId,
        OrderCourier $orderCourier,
        Context $context
    ): void {
        $config = $this->configRepository->getByShopId($shopId);

        $api = $this->apiClientResolver->getApi($shopId);

        /** @var OrderEntity $order */
        foreach ($orders as $order) {
            $pickupPayer = $this->pickupPayer->create($api->getMasterFid(), $config);

            $packageArrayByOrderId = $this->searchForOrderId($order->id, $packages);

            if ([] === $packageArrayByOrderId) {
                continue;
            }

            /** @var Package $packageByOrder */
            $packageByOrder = $packagesBySelectedOrdersArr[$packageArrayByOrderId['id']];

            $billingAddress = $order->billingAddress;

            if (null === $billingAddress) {
                continue;
            }

            $pickupCustomer = $this->pickupCustomer->create($billingAddress);

            $pickupSender = $this->pickupSender->create($config);

            $pickupParameters = $this->pickupParameters->create($order, $context);

            $pickupDetails = $this->pickupDetails->create(
                $pickupPayer,
                $pickupCustomer,
                $pickupSender,
                $pickupParameters
            );

            $dpdPickupParameters = $this->dpdPickupParameters->create($orderCourier, $pickupDetails);

            $request = new PackagesPickupCallV2Request();
            $request->setDpdPickupParams($dpdPickupParameters);

            $pickupRequest = $api->getPickupRequest($request);

            $packageByOrder->setOrderCourierNumber($pickupRequest->getReturn()->getOrderNumber());

            $this->entityManager->persist($packageByOrder);
        }

        $this->entityManager->flush();
    }

    private function searchForOrderId(string $id, array $array): array
    {
        foreach ($array as $val) {
            if ($val['orderId'] === $id) {
                return $val;
            }
        }

        return [];
    }
}
