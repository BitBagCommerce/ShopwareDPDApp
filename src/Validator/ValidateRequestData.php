<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Validator;

use BitBag\ShopwareDpdApp\AppSystem\Client\ClientInterface;
use BitBag\ShopwareDpdApp\Entity\ConfigInterface;
use BitBag\ShopwareDpdApp\Exception\ErrorNotificationException;
use BitBag\ShopwareDpdApp\Model\Order;
use BitBag\ShopwareDpdApp\Repository\ConfigRepositoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ValidateRequestData implements ValidatorRequestDataInterface
{
    private ConfigRepositoryInterface $configRepository;

    private TranslatorInterface $translator;

    public function __construct(ConfigRepositoryInterface $configRepository, TranslatorInterface $translator)
    {
        $this->configRepository = $configRepository;
        $this->translator = $translator;
    }

    public function validate(ClientInterface $client, Order $orderModel): void
    {
        $translator = $this->translator;

        /** @var ConfigInterface|null $config */
        $config = $this->configRepository->findByShopId($orderModel->getShopId());
        if (!$config) {
            throw new ErrorNotificationException($translator->trans('bitbag.shopware_dpd_app.order.config_not_found'));
        }

        if (!$config->getApiLogin() || !$config->getApiPassword() || !$config->getApiFid()) {
            throw new ErrorNotificationException($translator->trans('bitbag.shopware_dpd_app.order.config_dpd_data_not_found'));
        }

        if (!$config->getSenderFirstLastName() || !$config->getSenderStreet() || !$config->getSenderZipCode() ||
            !$config->getSenderCity() || !$config->getSenderPhoneNumber() || !$config->getSenderLocale()
        ) {
            throw new ErrorNotificationException($translator->trans('bitbag.shopware_dpd_app.order.config_sender_data_not_found'));
        }

        if (!$orderModel->getWeight()) {
            throw new ErrorNotificationException($translator->trans('bitbag.shopware_dpd_app.order.order_weight_null'));
        }

        if (!$orderModel->getEmail()) {
            throw new ErrorNotificationException($translator->trans('bitbag.shopware_dpd_app.order.email_not_found'));
        }

        if (!$orderModel->getPackage()->isValid()) {
            throw new ErrorNotificationException($translator->trans('bitbag.shopware_dpd_app.order.dpd_custom_fields_not_found'));
        }

        if (!$orderModel->getShippingAddress()->isValid()) {
            throw new ErrorNotificationException($translator->trans('bitbag.shopware_dpd_app.order.shipping_address_not_found'));
        }
    }
}
