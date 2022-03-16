<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\AppSystem\Controller;

use BitBag\ShopwareDpdApp\AppSystem\Authenticator\AuthenticatorInterface;
use BitBag\ShopwareDpdApp\Entity\Shop;
use BitBag\ShopwareDpdApp\Repository\ShopRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class RegistrationController
{
    private AuthenticatorInterface $authenticator;

    private EntityManagerInterface $entityManager;

    private ShopRepositoryInterface $shopRepository;

    private UrlGeneratorInterface $urlGenerator;

    private string $appName;

    private string $appSecret;

    public function __construct(
        AuthenticatorInterface $authenticator,
        EntityManagerInterface $entityManager,
        ShopRepositoryInterface $shopRepository,
        UrlGeneratorInterface $urlGenerator,
        string $appName,
        string $appSecret
    ) {
        $this->authenticator = $authenticator;
        $this->entityManager = $entityManager;
        $this->shopRepository = $shopRepository;
        $this->urlGenerator = $urlGenerator;
        $this->appName = $appName;
        $this->appSecret = $appSecret;
    }

    public function __invoke(Request $request): Response
    {
        if (!$this->authenticator->authenticateRegisterRequest($request)) {
            return new Response(null, 401);
        }

        $shopUrl = $this->getShopUrl($request);
        $shopId = $this->getShopId($request);

        if (null === $shopUrl || null === $shopId) {
            throw new BadRequestHttpException('Missing query parameters.');
        }

        $shop = $this->shopRepository->find($shopId);

        if (null !== $shop) {
            throw new BadRequestHttpException('Shop already exists.');
        }

        $secret = \bin2hex(\random_bytes(64));

        $shop = new Shop();

        $shop->setShopId($shopId);
        $shop->setShopUrl($shopUrl);
        $shop->setShopSecret($secret);

        $this->entityManager->persist($shop);
        $this->entityManager->flush();

        $proof = \hash_hmac('sha256', $shopId.$shopUrl.$this->appName, $this->appSecret);
        $body = [
            'proof' => $proof,
            'secret' => $secret,
            'confirmation_url' => $this->urlGenerator->generate('confirm_registration', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ];

        return new JsonResponse($body);
    }

    private function getShopUrl(Request $request): ?string
    {
        return $request->query->get('shop-url');
    }

    private function getShopId(Request $request): ?string
    {
        return $request->query->get('shop-id');
    }
}
