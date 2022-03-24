<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\AppSystem\ArgumentResolver;

use BitBag\ShopwareAppSkeleton\AppSystem\Authenticator\AuthenticatorInterface;
use BitBag\ShopwareAppSkeleton\AppSystem\Authenticator\OAuthAuthenticatorInterface;
use BitBag\ShopwareAppSkeleton\AppSystem\Client\ClientBuilder;
use BitBag\ShopwareAppSkeleton\AppSystem\Client\ClientInterface;
use BitBag\ShopwareAppSkeleton\Repository\ShopRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class ClientResolver implements ArgumentValueResolverInterface
{
    private ShopRepositoryInterface $shopRepository;

    private OAuthAuthenticatorInterface $apiAuthenticator;

    private AuthenticatorInterface $authenticator;

    public function __construct(
        ShopRepositoryInterface $shopRepository,
        AuthenticatorInterface $authenticator,
        OAuthAuthenticatorInterface $apiAuthenticator
    ) {
        $this->shopRepository = $shopRepository;
        $this->authenticator = $authenticator;
        $this->apiAuthenticator = $apiAuthenticator;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        if (ClientInterface::class !== $argument->getType()) {
            return false;
        }

        if ('POST' === $request->getMethod() && $this->supportsPostRequest($request)) {
            $requestContent = $request->toArray();

            /** @var array $source */
            $source = $requestContent['source'];

            /** @var string $shopId */
            $shopId = $source['shopId'];

            $shopSecret = $this->shopRepository->findSecretByShopId($shopId);

            if (null === $shopSecret) {
                return false;
            }

            return $this->authenticator->authenticatePostRequest($request, $shopSecret);
        }
        if ('GET' === $request->getMethod() && $this->supportsGetRequest($request)) {
            $shopId = $request->query->get('shop-id', '');
            $shopSecret = $this->shopRepository->findSecretByShopId($shopId);

            if (null === $shopSecret) {
                return false;
            }

            return $this->authenticator->authenticateGetRequest($request, $shopSecret);
        }

        return false;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ('POST' === $request->getMethod()) {
            $requestContent = $request->toArray();

            /** @var array $source */
            $source = $requestContent['source'];

            /** @var string $shopId */
            $shopId = $source['shopId'];
        } else {
            /** @var string $shopId */
            $shopId = $request->query->get('shop-id');
        }

        $shop = $this->shopRepository->getOneByShopId($shopId);

        $builder = new ClientBuilder($shop, $this->apiAuthenticator);

        yield $builder->buildClient();
    }

    private function supportsPostRequest(Request $request): bool
    {
        /** @var array{source?: array} $requestContent */
        $requestContent = $request->toArray();

        $hasSource = $requestContent && array_key_exists('source', $requestContent);

        if (!$hasSource) {
            return false;
        }

        $requiredKeys = ['url', 'shopId'];

        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $requestContent['source'])) {
                return false;
            }
        }

        return true;
    }

    private function supportsGetRequest(Request $request): bool
    {
        $query = $request->query->all();

        $requiredKeys = ['shop-url', 'shop-id', 'shopware-shop-signature', 'timestamp'];

        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $query)) {
                return false;
            }
        }

        return true;
    }
}
