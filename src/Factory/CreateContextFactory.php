<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use BitBag\ShopwareAppSystemBundle\Exception\ShopNotFoundException;
use BitBag\ShopwareAppSystemBundle\Factory\Context\ContextFactoryInterface;
use BitBag\ShopwareAppSystemBundle\Repository\ShopRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Vin\ShopwareSdk\Data\Context;

final class CreateContextFactory implements CreateContextFactoryInterface
{
    private ShopRepositoryInterface $shopRepository;

    private ContextFactoryInterface $contextFactory;

    public function __construct(ShopRepositoryInterface $shopRepository, ContextFactoryInterface $contextFactory)
    {
        $this->shopRepository = $shopRepository;
        $this->contextFactory = $contextFactory;
    }

    public function createByShopId(string $shopId): Context
    {
        $shop = $this->shopRepository->find($shopId);

        if (null === $shop) {
            throw new ShopNotFoundException($shopId);
        }

        $context = $this->contextFactory->create($shop);

        if (null === $context) {
            throw new UnauthorizedHttpException('');
        }

        return $context;
    }
}
