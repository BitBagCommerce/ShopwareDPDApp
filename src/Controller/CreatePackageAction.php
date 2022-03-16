<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Controller;

use BitBag\ShopwareDpdApp\AppSystem\Client\ClientInterface;
use BitBag\ShopwareDpdApp\Creator\CreatePackage;
use BitBag\ShopwareDpdApp\Entity\ShopInterface;
use BitBag\ShopwareDpdApp\Exception\ErrorNotificationException;
use BitBag\ShopwareDpdApp\Model\Order as OrderModel;
use BitBag\ShopwareDpdApp\Repository\ShopRepositoryInterface;
use BitBag\ShopwareDpdApp\Service\ClientApiService;
use BitBag\ShopwareDpdApp\Validator\ValidateRequestData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CreatePackageAction extends AbstractController
{
    private ShopRepositoryInterface $shopRepository;

    private TranslatorInterface $translator;

    private ValidateRequestData $validateRequestData;

    private CreatePackage $createPackage;

    private ClientApiService $clientApiService;

    public function __construct(
        ShopRepositoryInterface $shopRepository,
        TranslatorInterface $translator,
        ValidateRequestData $validateRequestData,
        CreatePackage $createPackage,
        ClientApiService $clientApiService
    ) {
        $this->shopRepository = $shopRepository;
        $this->translator = $translator;
        $this->validateRequestData = $validateRequestData;
        $this->createPackage = $createPackage;
        $this->clientApiService = $clientApiService;
    }

    public function __invoke(ClientInterface $client, Request $request): Response
    {
        $data = $request->toArray();

        $orderId = $data['data']['ids'][0];

        $shopId = $data['source']['shopId'];

        $order = $this->clientApiService->getOrder($client, $orderId);

        $shippingMethodName = $order['deliveries'][0]['shippingMethod']['name'];
        if (ShopInterface::SHIPPING_KEY !== $shippingMethodName) {
            exit;
        }

        $orderModel = new OrderModel($order, $shopId);

        try {
            $this->validateRequestData->validate($client, $orderModel);
        } catch (ErrorNotificationException $e) {
            return $this->returnNotificationError($e->getMessage(), $shopId);
        }

        try {
            $this->createPackage->create($orderModel);
        } catch (ErrorNotificationException $e) {
            return $this->returnNotificationError($e->getMessage(), $shopId);
        }

        $redirectUrl = $this->generateUrl(
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

    private function returnNotificationError(string $message, string $shopId): Response
    {
        $response = [
            'actionType' => 'notification',
            'payload' => [
                'status' => 'error',
                'message' => $message,
            ],
        ];

        return $this->sign($response, $shopId);
    }
}
