<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\Creator;

use BitBag\ShopwareAppSkeleton\Entity\ConfigInterface;
use BitBag\ShopwareAppSkeleton\Entity\Order as OrderEntity;
use BitBag\ShopwareAppSkeleton\Model\Order;
use BitBag\ShopwareAppSkeleton\Repository\ConfigRepositoryInterface;
use BitBag\ShopwareAppSkeleton\Repository\OrderRepositoryInterface;
use BitBag\ShopwareAppSkeleton\Repository\ShopRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use T3ko\Dpd\Api;
use T3ko\Dpd\Objects\Package;
use T3ko\Dpd\Objects\Parcel;
use T3ko\Dpd\Objects\Receiver;
use T3ko\Dpd\Objects\Sender;
use T3ko\Dpd\Request\GeneratePackageNumbersRequest;
use Twig\Environment;

class CreatePackage
{
    private ShopRepositoryInterface $shopRepository;

    private Environment $template;

    private ConfigRepositoryInterface $configRepository;

    private EntityManagerInterface $entityManager;

    private TranslatorInterface $translator;

    private OrderRepositoryInterface $orderRepository;

    public function __construct(
        ShopRepositoryInterface $shopRepository,
        Environment $template,
        ConfigRepositoryInterface $configRepository,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->shopRepository = $shopRepository;
        $this->template = $template;
        $this->configRepository = $configRepository;
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->orderRepository = $orderRepository;
    }

    public function create(Order $orderModel): array
    {
        $translator = $this->translator;
        $em = $this->entityManager;

        /** @var ConfigInterface $config */
        $config = $this->configRepository->findByShopId($orderModel->getShopId());

        $orderId = $orderModel->getOrderId();
        if (!$orderId) {
            return [
                'error' => true,
                'value' => 'Not found orderId',
            ];
        }

        $order = $this->orderRepository->findByOrderId($orderId);
        if ($order) {
            if ($order->getParcelId()) {
                return [
                    'error' => false,
                    'value' => $order->getParcelId(),
                ];
            }
        } else {
            $order = new OrderEntity();
        }

        $login = $config->getApiLogin();
        $password = $config->getApiPassword();
        $fid = $config->getApiFid();

        if (!$login || !$password || !$fid) {
            return [
                'error' => true,
                'value' => $translator->trans('bitbag.shopware_dpd_app.order.config_not_found'),
            ];
        }

        $api = new Api($login, $password, $fid);
        $api->setSandboxMode(true);

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
            return [
                'actionType' => 'notification',
                'payload' => [
                    'status' => 'error',
                    'message' => $translator->trans('bitbag.shopware_dpd_app.label.error_while_create_package'),
                ],
            ];
        }

        $parcelId = $response->getPackages()[0]->getParcels()[0]->getId();
        if (!$parcelId) {
            return [
                'actionType' => 'notification',
                'payload' => [
                    'status' => 'error',
                    'message' => $translator->trans('bitbag.shopware_dpd_app.label.not_found_parcel_id'),
                ],
            ];
        }

        $order->setShopId($orderModel->getShopId());
        $order->setOrderId($orderId);
        $order->setParcelId($parcelId);

        $em->persist($order);
        $em->flush();

        return [
            'error' => false,
            'value' => $parcelId,
        ];
    }
}
