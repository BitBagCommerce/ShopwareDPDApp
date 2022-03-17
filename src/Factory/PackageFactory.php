<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use BitBag\ShopwareDpdApp\Entity\ConfigInterface;
use BitBag\ShopwareDpdApp\Entity\Order as OrderEntity;
use BitBag\ShopwareDpdApp\Exception\ConfigNotFoundException;
use BitBag\ShopwareDpdApp\Exception\ErrorNotificationException;
use BitBag\ShopwareDpdApp\Model\Order;
use BitBag\ShopwareDpdApp\Repository\ConfigRepositoryInterface;
use BitBag\ShopwareDpdApp\Repository\OrderRepositoryInterface;
use BitBag\ShopwareDpdApp\Service\ApiService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use T3ko\Dpd\Objects\Package;
use T3ko\Dpd\Objects\Parcel;
use T3ko\Dpd\Objects\Receiver;
use T3ko\Dpd\Objects\Sender;
use T3ko\Dpd\Request\GeneratePackageNumbersRequest;

final class PackageFactory implements PackageFactoryInterface
{
    private ConfigRepositoryInterface $configRepository;

    private EntityManagerInterface $entityManager;

    private OrderRepositoryInterface $orderRepository;

    private ApiService $apiService;

    public function __construct(
        ConfigRepositoryInterface $configRepository,
        EntityManagerInterface $entityManager,
        OrderRepositoryInterface $orderRepository,
        ApiService $apiService
    ) {
        $this->configRepository = $configRepository;
        $this->entityManager = $entityManager;
        $this->orderRepository = $orderRepository;
        $this->apiService = $apiService;
    }

    public function create(Order $orderModel): int
    {
        $shopId = $orderModel->getShopId();
        if (!$shopId) {
            throw new ErrorNotificationException('bitbag.shopware_dpd_app.shop.not_found');
        }

        /** @var ConfigInterface $config */
        $config = $this->configRepository->findByShopId($shopId);

        $orderId = $orderModel->getOrderId();
        if (!$orderId) {
            throw new ErrorNotificationException('bitbag.shopware_dpd_app.order.not_found');
        }

        $order = $this->orderRepository->findByOrderId($orderId);
        if ($order) {
            if ($order->getParcelId()) {
                return $order->getParcelId();
            }
        } else {
            $order = new OrderEntity();
        }

        $fid = $config->getApiFid();

        try {
            $api = $this->apiService->getApi($orderModel->getShopId());
        } catch (ConfigNotFoundException $exception) {
            throw new ErrorNotificationException($exception->getMessage());
        }

        $sender = new Sender(
            $fid,
            $config->getSenderPhoneNumber(),
            $config->getSenderFirstLastName(),
            $config->getSenderStreet(),
            $config->getSenderZipCode(),
            $config->getSenderCity(),
            $config->getSenderLocale()
        );

        $address = $orderModel->getShippingAddress();

        $receiver = new Receiver(
            $address->getPhoneNumber(),
            $address->getFirstName().' '.$address->getLastName(),
            $address->getStreet(),
            $address->getZipCode(),
            $address->getCity(),
            $address->getCountryCode()
        );

        $package = $orderModel->getPackage();
        $parcel = new Parcel(
            $package->getWidth(),
            $package->getHeight(),
            $package->getDepth(),
            $orderModel->getWeight()
        );

        $package = new Package($sender, $receiver, [$parcel]);

        $request = GeneratePackageNumbersRequest::fromPackage($package);

        try {
            $response = $api->generatePackageNumbers($request);
        } catch (Exception $exception) {
            throw new ErrorNotificationException('bitbag.shopware_dpd_app.label.error_while_create_package');
        }

        $parcelId = $response->getPackages()[0]->getParcels()[0]->getId();
        if (!$parcelId) {
            throw new ErrorNotificationException('bitbag.shopware_dpd_app.label.not_found_parcel_id');
        }

        $order->setShopId($orderModel->getShopId());
        $order->setOrderId($orderId);
        $order->setParcelId($parcelId);

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $parcelId;
    }
}
