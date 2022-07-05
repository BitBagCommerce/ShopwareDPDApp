<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Controller;

use BitBag\ShopwareAppSystemBundle\Exception\ShopNotFoundException;
use BitBag\ShopwareAppSystemBundle\Repository\ShopRepositoryInterface;
use BitBag\ShopwareDpdApp\Entity\Config;
use BitBag\ShopwareDpdApp\Repository\ConfigRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class ConfigController extends AbstractController
{
    private ShopRepositoryInterface $shopRepository;

    private ConfigRepositoryInterface $configRepository;

    public function __construct(
        ShopRepositoryInterface $shopRepository,
        ConfigRepositoryInterface $configRepository
    ) {
        $this->shopRepository = $shopRepository;
        $this->configRepository = $configRepository;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $shopId = $request->query->get('shopId', '');
        $salesChannelId = $request->query->get('salesChannelId', '');

        $shop = $this->shopRepository->find($shopId);

        if (null === $shop) {
            throw new ShopNotFoundException($shopId);
        }

        $config = $this->configRepository->findByShopIdAndSalesChannelId($shopId, $salesChannelId) ?? new Config();

        return $this->json($config);
    }
}
