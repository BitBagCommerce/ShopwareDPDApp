<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\AppSystem\Authenticator;

use BitBag\ShopwareAppSkeleton\AppSystem\Credentials\CredentialsInterface;
use GuzzleHttp\HandlerStack;
use Symfony\Component\HttpFoundation\Request;

interface AuthenticatorInterface
{
    public function authenticate(CredentialsInterface $credentials, HandlerStack $handlerStack = null): CredentialsInterface;

    public function authenticateRegisterRequest(Request $request): bool;

    public function authenticatePostRequest(Request $request, string $shopSecret): bool;

    public function authenticateGetRequest(Request $request, string $shopSecret): bool;
}
