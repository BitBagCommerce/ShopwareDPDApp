<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\Controller;

use BitBag\ShopwareAppSkeleton\AppSystem\Client\ClientInterface;
use BitBag\ShopwareAppSkeleton\Repository\ShopRepositoryInterface;
use Exception;
use JsonException;
use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class OrderController
{
    private ShopRepositoryInterface $shopRepository;

    public function __construct(ShopRepositoryInterface $shopRepository)
    {
        $this->shopRepository = $shopRepository;
    }

    /**
     * @throws Exception
     */
    public function __invoke(ClientInterface $client, Request $request): Response
    {
        $this->checkSignature($request);

        $data = json_decode($request->getContent(), true, 512, \JSON_THROW_ON_ERROR);

        $orderId = $data['data']['ids'][0];

        $orderAddressFilter = [
            'filter' => [
                [
                    'type' => 'equals',
                    'field' => 'id',
                    'value' => $orderId,
                ],
            ],
            'associations' => [
                'lineItems' => [
                    'associations' => [
                        'product' => [],
                    ],
                ],
                'deliveries' => [],
            ],
        ];

        $order = $client->search('order', $orderAddressFilter);
        $lineItems = $order['data'][0]['lineItems'];

        $totalWeight = 0;

        foreach ($lineItems as $item) {
            $weight = $item['quantity'] * $item['product']['weight'];
            $totalWeight += $weight;
        }

        $shippingAddress = $order['data'][0]['deliveries'][0]['shippingOrderAddress'];

        $response = [
            'actionType' => 'openNewTab',
            'payload' => [
                'redirectUrl' => 'http://localhost',
            ],
        ];

        // https://developer.shopware.com/docs/guides/plugins/apps/administration/add-custom-action-button

        $shopId = $data['source']['shopId'];

        return $this->sign($response, $shopId);
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    private function checkSignature(Request $request): void
    {
        $requestContent = json_decode($request->getContent(), true, 512, \JSON_THROW_ON_ERROR);
        $shopId = $requestContent['source']['shopId'];

        // get the secret you have saved on registration for this shopId
        $shopSecret = $this->getSecretByShopId($shopId);

        $signature = $request->headers->get('shopware-shop-signature');
        if (null === $signature) {
            throw new RuntimeException('No signature provided signature');
        }

        $hmac = hash_hmac('sha256', $request->getContent(), $shopSecret);
        if (!hash_equals($hmac, $signature)) {
            throw new RuntimeException('Invalid signature');
        }
    }

    private function sign(array $content, string $shopId): JsonResponse
    {
        $response = new JsonResponse($content);

        // get the secret you have saved on registration for this shopId
        $secret = $this->getSecretByShopId($shopId);

        $hmac = hash_hmac('sha256', (string) $response->getContent(), $secret);

        $response->headers->set('shopware-app-signature', $hmac);

        return $response;
    }

    private function getSecretByShopId(string $shopId): string
    {
        return (string) $this->shopRepository->findSecretByShopId($shopId);
    }
}
