<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Controller;

use BitBag\ShopwareAppSystemBundle\Model\Action\ActionInterface;
use BitBag\ShopwareDpdApp\Exception\ErrorNotificationException;
use BitBag\ShopwareDpdApp\Exception\Order\OrderException;
use BitBag\ShopwareDpdApp\Factory\FeedbackResponseFactoryInterface;
use BitBag\ShopwareDpdApp\Factory\ShippingMethodPayloadFactoryInterface;
use BitBag\ShopwareDpdApp\Finder\OrderFinderInterface;
use BitBag\ShopwareDpdApp\Package\PackageServiceInterface;
use BitBag\ShopwareDpdApp\Repository\PackageRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vin\ShopwareSdk\Data\Context;

final class CreatePackageController
{
    private FeedbackResponseFactoryInterface $feedbackResponseFactory;

    private OrderFinderInterface $orderFinder;

    private PackageServiceInterface $packageService;

    private PackageRepositoryInterface $packageRepository;

    public function __construct(
        FeedbackResponseFactoryInterface $feedbackResponseFactory,
        OrderFinderInterface $orderFinder,
        PackageServiceInterface $packageService,
        PackageRepositoryInterface $packageRepository
    ) {
        $this->feedbackResponseFactory = $feedbackResponseFactory;
        $this->orderFinder = $orderFinder;
        $this->packageService = $packageService;
        $this->packageRepository = $packageRepository;
    }

    public function create(Request $request, ActionInterface $action, Context $context): Response
    {
        $language = $request->headers->get('sw-user-language', 'pl');

        $orderId = $action->getData()->getIds()[0] ?? null;

        try {
            $order = $this->orderFinder->getWithAssociations($orderId, $context);
        } catch (OrderException $e) {
            return $this->feedbackResponseFactory->returnError($e->getMessage(), $language);
        }

        $shopId = $action->getSource()->getShopId();

        $technicalName = $order->deliveries?->first()?->shippingMethod?->getTranslated()['customFields']['technical_name'] ?? null;

        if (ShippingMethodPayloadFactoryInterface::SHIPPING_KEY !== $technicalName) {
            return $this->feedbackResponseFactory->returnError(
                'bitbag.shopware_dpd_app.order.shipping_method.not_dpd',
                $language
            );
        }

        $package = $this->packageRepository->findByOrderId($orderId);

        if (null !== $package) {
            return $this->feedbackResponseFactory->returnWarning(
                'bitbag.shopware_dpd_app.package.already_created',
                $language
            );
        }

        try {
            $this->packageService->create($order, $shopId, $context);
        } catch (ErrorNotificationException $e) {
            return $this->feedbackResponseFactory->returnError($e->getMessage(), $language);
        }

        return $this->feedbackResponseFactory->returnSuccess(
            'bitbag.shopware_dpd_app.package.created',
            $language
        );
    }
}
