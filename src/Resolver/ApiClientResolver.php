<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Resolver;

use BitBag\ShopwareDpdApp\Entity\ConfigInterface;
use BitBag\ShopwareDpdApp\Repository\ConfigRepositoryInterface;
use T3ko\Dpd\Api;

final class ApiClientResolver implements ApiClientResolverInterface
{
    private ConfigRepositoryInterface $configRepository;

    public function __construct(ConfigRepositoryInterface $configRepository)
    {
        $this->configRepository = $configRepository;
    }

    public function getClient(string $shopId): Api
    {
        $config = $this->configRepository->getByShopId($shopId);

        $api = new Api(
            $config->getApiLogin(),
            $config->getApiPassword(),
            $config->getApiFid()
        );
        $api->setSandboxMode(ConfigInterface::SANDBOX_ENVIRONMENT === $config->getApiEnvironment());

        return $api;
    }
}
