<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Controller;

use BitBag\ShopwareDpdApp\AppSystem\Client\ClientInterface;
use BitBag\ShopwareDpdApp\AppSystem\Event\EventInterface;
use BitBag\ShopwareDpdApp\Exception\ErrorNotificationException;
use BitBag\ShopwareDpdApp\Factory\PackageFactory;
use BitBag\ShopwareDpdApp\Factory\PackageFactoryInterface;
use BitBag\ShopwareDpdApp\Factory\ShippingMethodFactoryInterface;
use BitBag\ShopwareDpdApp\Model\OrderModel;
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

    private PackageFactoryInterface $packageFactory;

    private ClientApiService $clientApiService;

    public function __construct(
        ShopRepositoryInterface $shopRepository,
        TranslatorInterface $translator,
        ValidateRequestData $validateRequestData,
        PackageFactory $packageFactory,
        ClientApiService $clientApiService
    ) {
        $this->shopRepository = $shopRepository;
        $this->translator = $translator;
        $this->validateRequestData = $validateRequestData;
        $this->packageFactory = $packageFactory;
        $this->clientApiService = $clientApiService;
    }

    public function __invoke(ClientInterface $client, Request $request, EventInterface $event): Response
    {
        $data = $request->toArray();

        $orderId = $data['data']['ids'][0];

        $shopId = $event->getShopId();

        $order = $this->clientApiService->getOrder($client, $orderId);

        $shippingMethodName = $order['deliveries'][0]['shippingMethod']['name'];
        if (ShippingMethodFactoryInterface::SHIPPING_KEY !== $shippingMethodName) {
            exit;
        }

        $orderModel = new OrderModel($order, $shopId);

        try {
            $this->validateRequestData->validate($client, $orderModel);
        } catch (ErrorNotificationException $e) {
            return $this->returnNotificationError($e->getMessage(), $shopId);
        }

        try {
            $this->packageFactory->create($orderModel);
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
                'message' => $this->translator->trans($message),
            ],
        ];

        return $this->sign($response, $shopId);
    }
}
