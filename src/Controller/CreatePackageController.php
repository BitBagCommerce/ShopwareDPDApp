<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Controller;

use BitBag\ShopwareAppSystemBundle\Model\Action\ActionInterface;
use BitBag\ShopwareDpdApp\Api\PackageServiceInterface;
use BitBag\ShopwareDpdApp\Entity\Package as PackageEntity;
use BitBag\ShopwareDpdApp\Exception\ErrorNotificationException;
use BitBag\ShopwareDpdApp\Exception\Order\OrderException;
use BitBag\ShopwareDpdApp\Factory\FeedbackResponseFactoryInterface;
use BitBag\ShopwareDpdApp\Factory\ShippingMethodPayloadFactoryInterface;
use BitBag\ShopwareDpdApp\Finder\OrderFinderInterface;
use BitBag\ShopwareDpdApp\Repository\PackageRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\OrderDelivery\OrderDeliveryEntity;
use Vin\ShopwareSdk\Repository\RepositoryInterface;

final class CreatePackageController
{
    private FeedbackResponseFactoryInterface $feedbackResponseFactory;

    private OrderFinderInterface $orderFinder;

    private PackageServiceInterface $packageService;

    private PackageRepositoryInterface $packageRepository;

    private EntityManagerInterface $entityManager;

    private RepositoryInterface $orderDeliveryRepository;

    public function __construct(
        FeedbackResponseFactoryInterface $feedbackResponseFactory,
        OrderFinderInterface $orderFinder,
        PackageServiceInterface $packageService,
        PackageRepositoryInterface $packageRepository,
        EntityManagerInterface $entityManager,
        RepositoryInterface $orderDeliveryRepository
    ) {
        $this->feedbackResponseFactory = $feedbackResponseFactory;
        $this->orderFinder = $orderFinder;
        $this->packageService = $packageService;
        $this->packageRepository = $packageRepository;
        $this->entityManager = $entityManager;
        $this->orderDeliveryRepository = $orderDeliveryRepository;
    }

    public function create(ActionInterface $action, Context $context): Response
    {
        $orderId = $action->getData()->getIds()[0] ?? null;

        try {
            $order = $this->orderFinder->getWithAssociations($orderId, $context);
        } catch (OrderException $e) {
            return $this->feedbackResponseFactory->createError($e->getMessage());
        }

        $shopId = $action->getSource()->getShopId();

        $shippingMethod = $order->deliveries?->first()?->shippingMethod ?? null;

        if (null === $shippingMethod) {
            return $this->feedbackResponseFactory->createError(
                'bitbag.shopware_dpd_app.order.shipping_method.not_found'
            );
        }

        $technicalName = $shippingMethod->getTranslated()['customFields']['technical_name'] ?? null;

        if (ShippingMethodPayloadFactoryInterface::SHIPPING_KEY !== $technicalName) {
            return $this->feedbackResponseFactory->createError(
                'bitbag.shopware_dpd_app.order.shipping_method.not_dpd'
            );
        }

        $package = $this->packageRepository->findByOrderId($orderId);

        if (null !== $package) {
            return $this->feedbackResponseFactory->createWarning(
                'bitbag.shopware_dpd_app.package.already_created'
            );
        }

        try {
            $packages = $this->packageService->create($order, $shopId, $context);

            $trackingCode = $packages[0]['waybill'];

            $this->createPackageEntity(
                $shopId,
                $order->id,
                $packages[0]['id'],
                $trackingCode
            );

            $this->addTrackingCodeToOrderDelivery(
                $order->deliveries?->first(),
                $trackingCode,
                $context
            );
        } catch (ErrorNotificationException $e) {
            return $this->feedbackResponseFactory->createError($e->getMessage());
        }

        return $this->feedbackResponseFactory->createSuccess(
            'bitbag.shopware_dpd_app.package.created'
        );
    }

    private function createPackageEntity(
        string $shopId,
        string $orderId,
        int $parcelId,
        string $trackingCode
    ): void {
        $packageEntity = new PackageEntity();
        $packageEntity->setShopId($shopId);
        $packageEntity->setOrderId($orderId);
        $packageEntity->setParcelId($parcelId);
        $packageEntity->setWaybill($trackingCode);

        $this->entityManager->persist($packageEntity);
        $this->entityManager->flush();
    }

    private function addTrackingCodeToOrderDelivery(
        ?OrderDeliveryEntity $orderDelivery,
        string $trackingCode,
        Context $context
    ): void {
        $trackingCodes = $orderDelivery->trackingCodes ?? [];

        if (null !== $orderDelivery &&
            !in_array($trackingCode, $trackingCodes)
        ) {
            $this->orderDeliveryRepository->update([
                'id' => $orderDelivery->id,
                'trackingCodes' => array_merge($trackingCodes, [$trackingCode]),
            ], $context);
        }
    }
}
