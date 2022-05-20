<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Api;

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
        $config = $this->configRepository->findByShopId($shopId);

        if (null === $config) {
            throw new ConfigNotFoundException('bitbag.shopware_dpd_app.config.credentialsDataNotFound');
        }

        $login = $config->getApiLogin();
        $password = $config->getApiPassword();
        $fid = $config->getApiFid();
        $environment = $config->getApiEnvironment();

        if (null === $login || null === $password || null === $fid || null === $environment) {
            throw new ConfigNotFoundException('bitbag.shopware_dpd_app.config.credentialsDataNotFound');
        }

        $api = new Api($login, $password, $fid);
        $api->setSandboxMode(WebClientInterface::SANDBOX_ENVIRONMENT === $environment);

        return $api;
    }
}
