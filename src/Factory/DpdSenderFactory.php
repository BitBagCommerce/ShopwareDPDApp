<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use BitBag\ShopwareDpdApp\Exception\ErrorNotificationException;
use BitBag\ShopwareDpdApp\Repository\ConfigRepositoryInterface;
use T3ko\Dpd\Objects\Sender;

final class DpdSenderFactory implements DpdSenderFactoryInterface
{
    private ConfigRepositoryInterface $configRepository;

    public function __construct(ConfigRepositoryInterface $configRepository)
    {
        $this->configRepository = $configRepository;
    }

    public function create(string $shopId, string $salesChannelId): Sender
    {
        try {
            $config = $this->configRepository->getByShopIdAndSalesChannelId($shopId, $salesChannelId);
        } catch (ErrorNotificationException) {
            $config = $this->configRepository->getByShopIdAndSalesChannelId($shopId, '');
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
