<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Controller;

use BitBag\ShopwareDpdApp\Exception\ConfigNotFoundException;
use BitBag\ShopwareDpdApp\Exception\OrderNotFoundException;
use BitBag\ShopwareDpdApp\Repository\OrderRepositoryInterface;
use BitBag\ShopwareDpdApp\Service\ApiService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use T3ko\Dpd\Request\GenerateLabelsRequest;

final class ShowLabelAction
{
    private OrderRepositoryInterface $orderRepository;

    private TranslatorInterface $translator;

    private ApiService $apiService;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        TranslatorInterface $translator,
        ApiService $apiService
    ) {
        $this->orderRepository = $orderRepository;
        $this->translator = $translator;
        $this->apiService = $apiService;
    }

    public function __invoke(string $orderId): Response
    {
        $translator = $this->translator;

        $order = $this->orderRepository->findByOrderId($orderId);
        if (!$order) {
            throw new OrderNotFoundException($translator->trans('bitbag.shopware_dpd_app.order.not_found'));
        }

        if (!$order->getParcelId()) {
            throw new OrderNotFoundException($translator->trans('bitbag.shopware_dpd_app.label.not_found_parcel_id'));
        }

        try {
            $api = $this->apiService->getApi($order->getShopId());
        } catch (ConfigNotFoundException $exception) {
            throw new ConfigNotFoundException($translator->trans($exception->getMessage()));
        }

        $requestLabels = GenerateLabelsRequest::fromParcelIds([$order->getParcelId()]);

        $labelResponse = $api->generateLabels($requestLabels);

        $filename = sprintf('filename="order_%s.pdf"', $orderId);

        $response = new Response($labelResponse->getFileContent());
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Content-Disposition', $filename);

        return $response;
    }
}
