<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\AppSystem\Authenticator;

use BitBag\ShopwareAppSkeleton\AppSystem\Credentials\OAuthCredentials;
use BitBag\ShopwareAppSkeleton\AppSystem\Credentials\OAuthCredentialsInterface;
use BitBag\ShopwareAppSkeleton\AppSystem\Exception\AuthenticationException;
use BitBag\ShopwareAppSkeleton\Entity\ShopInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

final class OAuthAuthenticator implements OAuthAuthenticatorInterface
{
    public function authenticate(ShopInterface $shop): OAuthCredentialsInterface
    {
        $shopUrl = $shop->getShopUrl();
        $key = $shop->getApiKey() ?? '';
        $secretKey = $shop->getSecretKey() ?? '';

        $authClient = new Client(['base_uri' => $shopUrl]);

        try {
            $response = $authClient->post('/api/oauth/token', [
                'json' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $key,
                    'client_secret' => $secretKey,
                ],
            ]);
        } catch (RequestException $e) {
            throw new AuthenticationException($shopUrl, $key, $e->getMessage());
        }

        if (200 !== $response->getStatusCode()) {
            throw new AuthenticationException($shopUrl, $key, $response->getBody()->getContents());
        }

        /** @var array{
                token_type: string,
         *      access_token: string,
         *      expires_in: string
         * } $jsonResponse
         */
        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        return new OAuthCredentials(
            $jsonResponse['token_type'],
            $jsonResponse['expires_in'],
            $jsonResponse['access_token']
        );
    }
}
