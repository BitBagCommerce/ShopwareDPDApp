<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Service;

use BitBag\ShopwareDpdApp\Entity\ConfigInterface;
use BitBag\ShopwareDpdApp\Exception\ConfigNotFoundException;
use BitBag\ShopwareDpdApp\Repository\ConfigRepositoryInterface;
use T3ko\Dpd\Api;

final class ApiService
{
    private ConfigRepositoryInterface $configRepository;

    public function __construct(ConfigRepositoryInterface $configRepository)
    {
        $this->configRepository = $configRepository;
    }

    public function getApi(string $shopId): Api
    {
        /** @var ConfigInterface $config */
        $config = $this->configRepository->findByShopId($shopId);

        $login = $config->getApiLogin();
        $password = $config->getApiPassword();
        $fid = $config->getApiFid();

        if (!$login || !$password || !$fid) {
            throw new ConfigNotFoundException('bitbag.shopware_dpd_app.order.config_not_found');
        }

        $api = new Api($login, $password, $fid);
        $api->setSandboxMode(true);

        return $api;
    }
}
