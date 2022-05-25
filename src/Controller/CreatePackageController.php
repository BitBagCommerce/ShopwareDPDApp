<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Controller;

use BitBag\ShopwareAppSystemBundle\Model\Action\ActionInterface;
use BitBag\ShopwareDpdApp\Exception\ErrorNotificationException;
use BitBag\ShopwareDpdApp\Exception\Order\OrderException;
use BitBag\ShopwareDpdApp\Factory\ShippingMethodPayloadFactoryInterface;
use BitBag\ShopwareDpdApp\Finder\OrderFinderInterface;
use BitBag\ShopwareDpdApp\Package\PackageServiceInterface;
use BitBag\ShopwareDpdApp\Provider\NotificationProviderInterface;
use BitBag\ShopwareDpdApp\Repository\PackageRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vin\ShopwareSdk\Data\Context;

final class CreatePackageController
{
    private NotificationProviderInterface $notificationProvider;

    private OrderFinderInterface $orderFinder;

    private PackageServiceInterface $packageService;

    private PackageRepositoryInterface $packageRepository;

    public function __construct(
        NotificationProviderInterface $notificationProvider,
        OrderFinderInterface $orderFinder,
        PackageServiceInterface $packageService,
        PackageRepositoryInterface $packageRepository
    ) {
        $this->notificationProvider = $notificationProvider;
        $this->orderFinder = $orderFinder;
        $this->packageService = $packageService;
        $this->packageRepository = $packageRepository;
    }

    public function create(Request $request, ActionInterface $action, Context $context): Response
    {
        $language = $request->headers->get('sw-user-language', 'pl');

        $data = $request->toArray();

        $orderId = $data['data']['ids'][0] ?? null;

        try {
            $order = $this->orderFinder->getWithAssociations($orderId, $context);
        } catch (OrderException $exception) {
            return $this->notificationProvider->returnNotificationError($exception->getMessage(), $language);
        }

        $shopId = $action->getSource()->getShopId();

        if (ShippingMethodPayloadFactoryInterface::SHIPPING_KEY !== $order->deliveries?->first()?->shippingMethod?->name) {
            return $this->notificationProvider->returnNotificationError(
                'bitbag.shopware_dpd_app.order.shipping_method.not_dpd',
                $language
            );
        }

        $package = $this->packageRepository->findByOrderId($orderId);

        if (null !== $package) {
            return $this->notificationProvider->returnNotificationSuccess(
                'bitbag.shopware_dpd_app.package.already_created',
                $language
            );
        }

        try {
            $this->packageService->create($order, $shopId, $context);
        } catch (ErrorNotificationException $exception) {
            return $this->notificationProvider->returnNotificationError($exception->getMessage(), $language);
        }

        return $this->notificationProvider->returnNotificationSuccess(
            'bitbag.shopware_dpd_app.parcel.created',
            $language
        );
    }
}
