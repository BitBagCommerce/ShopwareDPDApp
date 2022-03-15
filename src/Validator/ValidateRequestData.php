<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\Validator;

use BitBag\ShopwareAppSkeleton\AppSystem\Client\ClientInterface;
use BitBag\ShopwareAppSkeleton\Entity\ConfigInterface;
use BitBag\ShopwareAppSkeleton\Model\Order;
use BitBag\ShopwareAppSkeleton\Repository\ConfigRepositoryInterface;

class ValidateRequestData implements ValidatorRequestDataInterface
{
    private ConfigRepositoryInterface $configRepository;

    public function __construct(ConfigRepositoryInterface $configRepository)
    {
        $this->configRepository = $configRepository;
    }

    public function validate(ClientInterface $client, Order $orderModel): array
    {
        /** @var ConfigInterface|null $config */
        $config = $this->configRepository->findByShopId($orderModel->getShopId());
        if (!$config) {
            return $this->returnErrorNotificationMessage(
                'bitbag.shopware_skeleton_app.order.config_not_found'
            );
        }

        if (!$config->getApiLogin() || !$config->getApiPassword() || !$config->getApiFid()) {
            return $this->returnErrorNotificationMessage(
                'bitbag.shopware_skeleton_app.order.config_dpd_data_not_found'
            );
        }

        if (!$config->getSenderFirstLastName() || !$config->getSenderStreet() || !$config->getSenderZipCode() ||
            !$config->getSenderCity() || !$config->getSenderPhoneNumber() || !$config->getSenderLocale()
        ) {
            return $this->returnErrorNotificationMessage(
                'bitbag.shopware_skeleton_app.order.config_sender_data_not_found'
            );
        }

        if (!$orderModel->getWeight()) {
            return $this->returnErrorNotificationMessage(
                'bitbag.shopware_skeleton_app.order.order_weight_null'
            );
        }

        if (!$orderModel->getEmail()) {
            return $this->returnErrorNotificationMessage(
                'bitbag.shopware_skeleton_app.order.email_not_found',
            );
        }

        if (!$orderModel->getPackage()->isValid()) {
            return $this->returnErrorNotificationMessage(
                'bitbag.shopware_skeleton_app.order.dpd_custom_fields_not_found'
            );
        }

        if (!$orderModel->getShippingAddress()->isValid()) {
            return $this->returnErrorNotificationMessage(
                'bitbag.shopware_skeleton_app.order.shipping_address_not_found',
            );
        }

        return ['error' => false];
    }

    private function returnErrorNotificationMessage(string $messageKey): array
    {
        return [
            'error' => true,
            'messageKey' => $messageKey,
        ];
    }
}
