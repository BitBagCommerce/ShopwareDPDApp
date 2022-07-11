<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Validator;

use BitBag\ShopwareDpdApp\Exception\ErrorNotificationException;
use BitBag\ShopwareDpdApp\Exception\Order\OrderException;
use BitBag\ShopwareDpdApp\Factory\ContextFactoryInterface;
use BitBag\ShopwareDpdApp\Finder\OrderFinderInterface;
use BitBag\ShopwareDpdApp\Repository\ConfigRepositoryInterface;

final class ConfigValidator implements ConfigValidatorInterface
{
    private ContextFactoryInterface $contextFactory;

    private OrderFinderInterface $orderFinder;

    private ConfigRepositoryInterface $configRepository;

    public function __construct(
        ContextFactoryInterface $contextFactory,
        OrderFinderInterface $orderFinder,
        ConfigRepositoryInterface $configRepository
    ) {
        $this->contextFactory = $contextFactory;
        $this->orderFinder = $orderFinder;
        $this->configRepository = $configRepository;
    }

    public function checkApiDataFilled(string $shopId, string $orderId): void
    {
        $context = $this->contextFactory->createByShopId($shopId);

        try {
            $salesChannelId = $this->orderFinder->getSalesChannelIdByOrderId($orderId, $context);
        } catch (OrderException $e) {
            throw new ErrorNotificationException($e->getMessage());
        }

        try {
            $this->configRepository->getByShopIdAndSalesChannelId($shopId, $salesChannelId);
        } catch (ErrorNotificationException $e) {
            throw new ErrorNotificationException($e->getMessage());
        }
    }
}
