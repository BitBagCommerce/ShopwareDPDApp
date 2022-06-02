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

final class CreatePackageController
{
    private FeedbackResponseFactoryInterface $feedbackResponseFactory;

    private OrderFinderInterface $orderFinder;

    private PackageServiceInterface $packageService;

    private PackageRepositoryInterface $packageRepository;

    private EntityManagerInterface $entityManager;

    public function __construct(
        FeedbackResponseFactoryInterface $feedbackResponseFactory,
        OrderFinderInterface $orderFinder,
        PackageServiceInterface $packageService,
        PackageRepositoryInterface $packageRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->feedbackResponseFactory = $feedbackResponseFactory;
        $this->orderFinder = $orderFinder;
        $this->packageService = $packageService;
        $this->packageRepository = $packageRepository;
        $this->entityManager = $entityManager;
    }

    public function create(ActionInterface $action, Context $context): Response
    {
        $orderId = $action->getData()->getIds()[0] ?? null;

        try {
            $order = $this->orderFinder->getWithAssociations($orderId, $context);
        } catch (OrderException $e) {
            return $this->feedbackResponseFactory->returnError($e->getMessage());
        }

        $shopId = $action->getSource()->getShopId();

        $shippingMethod = $order->deliveries?->first()?->shippingMethod ?? null;

        if (null === $shippingMethod) {
            return $this->feedbackResponseFactory->returnError(
                'bitbag.shopware_dpd_app.order.shipping_method.not_found'
            );
        }

        $technicalName = $shippingMethod->getTranslated()['customFields']['technical_name'] ?? null;

        if (ShippingMethodPayloadFactoryInterface::SHIPPING_KEY !== $technicalName) {
            return $this->feedbackResponseFactory->returnError(
                'bitbag.shopware_dpd_app.order.shipping_method.not_dpd'
            );
        }

        $package = $this->packageRepository->findByOrderId($orderId);

        if (null !== $package) {
            return $this->feedbackResponseFactory->returnWarning(
                'bitbag.shopware_dpd_app.package.already_created'
            );
        }

        try {
            $packages = $this->packageService->create($order, $shopId, $context);

            $packageEntity = new PackageEntity();
            $packageEntity->setShopId($shopId);
            $packageEntity->setOrderId($order->id);
            $packageEntity->setParcelId($packages[0]['id']);
            $packageEntity->setWaybill($packages[0]['waybill']);

            $this->entityManager->persist($packageEntity);
            $this->entityManager->flush();
        } catch (ErrorNotificationException $e) {
            return $this->feedbackResponseFactory->returnError($e->getMessage());
        }

        return $this->feedbackResponseFactory->returnSuccess(
            'bitbag.shopware_dpd_app.package.created'
        );
    }
}
