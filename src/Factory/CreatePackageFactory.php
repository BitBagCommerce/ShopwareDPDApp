<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use BitBag\ShopwareDpdApp\Exception\ConfigNotFoundException;
use BitBag\ShopwareDpdApp\Exception\ErrorNotificationException;
use BitBag\ShopwareDpdApp\Model\OrderModelInterface;
use BitBag\ShopwareDpdApp\Service\ApiService;
use Exception;
use T3ko\Dpd\Objects\Package;
use T3ko\Dpd\Request\GeneratePackageNumbersRequest;

final class CreatePackageFactory implements CreatePackageFactoryInterface
{
    private ApiService $apiService;

    private CreateDpdSenderFactoryInterface $createDpdSender;

    private CreateDpdReceiverFactoryInterface $createDpdReceiver;

    private CreateDpdParcelFactoryInterface $createDpdParcel;

    public function __construct(
        ApiService $apiService,
        CreateDpdSenderFactoryInterface $createDpdSender,
        CreateDpdReceiverFactoryInterface $createDpdReceiver,
        CreateDpdParcelFactoryInterface $createDpdParcel
    ) {
        $this->apiService = $apiService;
        $this->createDpdSender = $createDpdSender;
        $this->createDpdReceiver = $createDpdReceiver;
        $this->createDpdParcel = $createDpdParcel;
    }

    public function create(OrderModelInterface $orderModel): int
    {
        try {
            $api = $this->apiService->getApi($orderModel->getShopId());
        } catch (ConfigNotFoundException $exception) {
            throw new ErrorNotificationException($exception->getMessage());
        }

        try {
            $sender = $this->createDpdSender->create($orderModel->getShopId());
        } catch (ConfigNotFoundException $exception) {
            throw new ErrorNotificationException($exception->getMessage());
        }

        $receiver = $this->createDpdReceiver->create($orderModel->getShippingAddress());

        $parcel = $this->createDpdParcel->create($orderModel->getPackage(), $orderModel->getWeight());

        $package = new Package($sender, $receiver, [$parcel]);

        $request = GeneratePackageNumbersRequest::fromPackage($package);

        try {
            $response = $api->generatePackageNumbers($request);
        } catch (Exception $exception) {
            throw new ErrorNotificationException('bitbag.shopware_dpd_app.label.error_while_create_package');
        }

        if (empty($response->getPackages()) || empty($response->getPackages()[0]->getParcels()) ||
            !$parcelId = $response->getPackages()[0]->getParcels()[0]->getId()
        ) {
            throw new ErrorNotificationException('bitbag.shopware_dpd_app.label.not_found_parcel_id');
        }

        return $parcelId;
    }
}
