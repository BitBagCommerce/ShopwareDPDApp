<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Service;

use BitBag\ShopwareDpdApp\Entity\ConfigInterface;
use BitBag\ShopwareDpdApp\Exception\ConfigNotFoundException;
use BitBag\ShopwareDpdApp\Repository\ConfigRepositoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use T3ko\Dpd\Api;

class ApiService
{
    private ConfigRepositoryInterface $configRepository;

    private TranslatorInterface $translator;

    public function __construct(ConfigRepositoryInterface $configRepository, TranslatorInterface $translator)
    {
        $this->configRepository = $configRepository;
        $this->translator = $translator;
    }

    public function getApi(string $shopId): Api
    {
        /** @var ConfigInterface $config */
        $config = $this->configRepository->findByShopId($shopId);

        $login = $config->getApiLogin();
        $password = $config->getApiPassword();
        $fid = $config->getApiFid();

        if (!$login || !$password || !$fid) {
            throw new ConfigNotFoundException($this->translator->trans('bitbag.shopware_dpd_app.order.config_not_found'));
        }

        $api = new Api($login, $password, $fid);
        $api->setSandboxMode(true);

        return $api;
    }
}
