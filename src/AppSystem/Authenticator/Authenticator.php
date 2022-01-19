<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\AppSystem\Authenticator;

use BitBag\ShopwareAppSkeleton\AppSystem\Credentials\CredentialsInterface;
use BitBag\ShopwareAppSkeleton\AppSystem\Exception\AuthenticationException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use Symfony\Component\HttpFoundation\Request;

final class Authenticator implements AuthenticatorInterface
{
    private string $appSecret;

    public function __construct(string $appSecret)
    {
        $this->appSecret = $appSecret;
    }

    public function authenticate(CredentialsInterface $credentials, HandlerStack $handlerStack = null): CredentialsInterface
    {
        $shopUrl = $credentials->getShopUrl();
        $key = $credentials->getApiKey();
        $secretKey = $credentials->getSecretKey();

        $authClient = new Client(['base_uri' => $shopUrl, 'handler' => $handlerStack]);

        try {
            $response = $authClient->post('/api/oauth/token', [
                'json' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $key,
                    'client_secret' => $secretKey,
                ],
            ]);
        } catch (RequestException $e) {
            throw new AuthenticationException($shopUrl, $key, 'Something went wrong. Cannot connect to the server.');
        }

        if (200 !== $response->getStatusCode()) {
            throw new AuthenticationException($shopUrl, $key, $response->getBody()->getContents());
        }

        /** @var array $jsonResponse */
        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        /** @var string $token */
        $token = $jsonResponse['access_token'];

        return $credentials->withToken($token);
    }

    public function authenticateRegisterRequest(Request $request): bool
    {
        $signature = $request->headers->get('shopware-app-signature', '');
        $queryString = rawurldecode($request->getQueryString() ?? '');

        $hmac = \hash_hmac('sha256', $queryString, $this->appSecret);

        return \hash_equals($hmac, $signature);
    }

    public function authenticatePostRequest(Request $request, string $shopSecret): bool
    {
        if (!array_key_exists('shopware-shop-signature', $request->headers->all())) {
            return false;
        }

        $signature = $request->headers->get('shopware-shop-signature', '');

        $hmac = \hash_hmac('sha256', $request->getContent(), $shopSecret);

        return \hash_equals($hmac, $signature);
    }

    public function authenticateGetRequest(Request $request, string $shopSecret): bool
    {
        $query = $request->query->all();

        /** @var string|null $shopSignature */
        $shopSignature = $query['shopware-shop-signature'];

        if (null === $shopSignature) {
            return false;
        }

        unset($query['shopware-shop-signature']);
        $queryString = http_build_query($query);

        $hmac = \hash_hmac('sha256', $queryString, $shopSecret);

        return \hash_equals($hmac, $shopSignature);
    }
}
