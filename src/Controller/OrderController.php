<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Controller;

use BitBag\ShopwareDpdApp\AppSystem\Client\ClientInterface;
use BitBag\ShopwareDpdApp\Entity\ShopInterface;
use BitBag\ShopwareDpdApp\Creator\CreatePackage;
use BitBag\ShopwareDpdApp\Model\Order;
use BitBag\ShopwareDpdApp\Repository\ShopRepositoryInterface;
use BitBag\ShopwareDpdApp\Validator\ValidateRequestData;
use Exception;
use JsonException;
use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class OrderController
{
    private ShopRepositoryInterface $shopRepository;

    private RouterInterface $router;

    private TranslatorInterface $translator;

    private ValidateRequestData $validateRequestData;

    private CreatePackage $createPackage;

    public function __construct(
        ShopRepositoryInterface $shopRepository,
        RouterInterface $router,
        TranslatorInterface $translator,
        ValidateRequestData $validateRequestData,
        CreatePackage $createPackage
    ) {
        $this->shopRepository = $shopRepository;
        $this->router = $router;
        $this->translator = $translator;
        $this->validateRequestData = $validateRequestData;
        $this->createPackage = $createPackage;
    }

    /**
     * @throws Exception
     */
    public function __invoke(
        ClientInterface $client,
        Request $request
    ): Response {
        $this->checkSignature($request);

        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $orderId = $data['data']['ids'][0];

        $shopId = $data['source']['shopId'];

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
                'deliveries' => [
                    'associations' => [
                        'shippingMethod' => [],
                    ],
                ],
            ],
        ];

        $order = $client->search('order', $orderAddressFilter);

        $shippingMethodName = $order['data'][0]['deliveries'][0]['shippingMethod']['name'];
        if (ShopInterface::SHIPPING_KEY !== $shippingMethodName) {
            exit;
        }

        $orderModel = new Order($order, $shopId);

        $validator = $this->validateRequestData->validate($client, $orderModel);
        if (true === $validator['error']) {
            $response = [
                'actionType' => 'notification',
                'payload' => [
                    'status' => 'error',
                    'message' => $this->translator->trans($validator['messageKey']),
                ],
            ];

            return $this->sign($response, $shopId);
        }

        $createPackage = $this->createPackage->create($orderModel);
        if (isset($createPackage['actionType'])) {
            return $this->sign($createPackage, $shopId);
        }

        $redirectUrl = $this->router->generate(
            'get_label_pdf',
            ['orderId' => $orderId],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $response = [
            'actionType' => 'openNewTab',
            'payload' => [
                'redirectUrl' => $redirectUrl,
            ],
        ];

        return $this->sign($response, $shopId);
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    private function checkSignature(Request $request): void
    {
        $requestContent = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
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
