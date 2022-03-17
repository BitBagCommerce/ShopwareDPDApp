<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Validator;

use BitBag\ShopwareDpdApp\AppSystem\Client\ClientInterface;
use BitBag\ShopwareDpdApp\Entity\ConfigInterface;
use BitBag\ShopwareDpdApp\Exception\ErrorNotificationException;
use BitBag\ShopwareDpdApp\Model\OrderModel;
use BitBag\ShopwareDpdApp\Repository\ConfigRepositoryInterface;

final class ValidateRequestData implements ValidatorRequestDataInterface
{
    private ConfigRepositoryInterface $configRepository;

    public function __construct(ConfigRepositoryInterface $configRepository)
    {
        $this->configRepository = $configRepository;
    }

    public function validate(ClientInterface $client, OrderModel $orderModel): void
    {
        /** @var ConfigInterface|null $config */
        $config = $this->configRepository->findByShopId($orderModel->getShopId());
        if (!$config) {
            throw new ErrorNotificationException('bitbag.shopware_dpd_app.order.config_not_found');
        }

        if (!$config->getApiLogin() || !$config->getApiPassword() || !$config->getApiFid()) {
            throw new ErrorNotificationException('bitbag.shopware_dpd_app.order.config_dpd_data_not_found');
        }

        if (!$config->getSenderFirstLastName() || !$config->getSenderStreet() || !$config->getSenderZipCode() ||
            !$config->getSenderCity() || !$config->getSenderPhoneNumber() || !$config->getSenderLocale()
        ) {
            throw new ErrorNotificationException('bitbag.shopware_dpd_app.order.config_sender_data_not_found');
        }

        if (!$orderModel->getWeight()) {
            throw new ErrorNotificationException('bitbag.shopware_dpd_app.order.order_weight_null');
        }

        if (!$orderModel->getEmail()) {
            throw new ErrorNotificationException('bitbag.shopware_dpd_app.order.email_not_found');
        }

        if (!$orderModel->getPackage()->isValid()) {
            throw new ErrorNotificationException('bitbag.shopware_dpd_app.order.dpd_custom_fields_not_found');
        }

        if (!$orderModel->getShippingAddress()->isValid()) {
            throw new ErrorNotificationException('bitbag.shopware_dpd_app.order.shipping_address_not_found');
        }
    }
}
