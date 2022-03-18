<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use BitBag\ShopwareDpdApp\Exception\ErrorNotificationException;
use BitBag\ShopwareDpdApp\Repository\ConfigRepositoryInterface;
use T3ko\Dpd\Objects\Sender;

final class CreateDpdSenderFactory implements CreateDpdSenderFactoryInterface
{
    private ConfigRepositoryInterface $configRepository;

    public function __construct(ConfigRepositoryInterface $configRepository)
    {
        $this->configRepository = $configRepository;
    }

    public function create(string $shopId): Sender
    {
        $config = $this->configRepository->findByShopId($shopId);
        if (!$config) {
            throw new ErrorNotificationException('bitbag.shopware_dpd_app.order.config_not_found');
        }

        return new Sender(
            $config->getApiFid(),
            $config->getSenderPhoneNumber(),
            $config->getSenderFirstLastName(),
            $config->getSenderStreet(),
            $config->getSenderZipCode(),
            $config->getSenderCity(),
            $config->getSenderLocale()
        );
    }
}
