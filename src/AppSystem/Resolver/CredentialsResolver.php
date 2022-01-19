<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\AppSystem\Resolver;

use BitBag\ShopwareAppSkeleton\AppSystem\Credentials\Credentials;
use BitBag\ShopwareAppSkeleton\AppSystem\Credentials\CredentialsInterface;
use BitBag\ShopwareAppSkeleton\AppSystem\Exception\CredentialsResolverException;
use BitBag\ShopwareAppSkeleton\Repository\ShopRepositoryInterface;

final class CredentialsResolver implements CredentialsResolverInterface
{
    private ShopRepositoryInterface $shopRepository;

    public function __construct(ShopRepositoryInterface $shopRepository)
    {
        $this->shopRepository = $shopRepository;
    }

    public function resolveByShopId(string $shopId): CredentialsInterface
    {
        $shop = $this->shopRepository->find($shopId);

        if (null === $shop) {
            throw new CredentialsResolverException(\sprintf('Could not find valid shop with id: %s', $shopId));
        }

        $apiKey = $shop->getApiKey();
        $secretKey = $shop->getSecretKey();

        if (null === $apiKey || null === $secretKey) {
            throw new CredentialsResolverException('Missing credentials');
        }

        return new Credentials(
            $shop->getShopUrl(),
            $apiKey,
            $secretKey
        );
    }
}
