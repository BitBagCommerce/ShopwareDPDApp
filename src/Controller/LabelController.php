<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Controller;

use BitBag\ShopwareDpdApp\Entity\ConfigInterface;
use BitBag\ShopwareDpdApp\Exception\OrderNotFoundException;
use BitBag\ShopwareDpdApp\Repository\ConfigRepositoryInterface;
use BitBag\ShopwareDpdApp\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use T3ko\Dpd\Api;
use T3ko\Dpd\Request\GenerateLabelsRequest;

final class LabelController
{
    private ConfigRepositoryInterface $configRepository;

    private OrderRepositoryInterface $orderRepository;

    private TranslatorInterface $translator;

    public function __construct(
        ConfigRepositoryInterface $configRepository,
        OrderRepositoryInterface $orderRepository,
        TranslatorInterface $translator
    ) {
        $this->configRepository = $configRepository;
        $this->orderRepository = $orderRepository;
        $this->translator = $translator;
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

        /** @var ConfigInterface $config */
        $config = $this->configRepository->findByShopId($order->getShopId());

        $login = $config->getApiLogin();
        $password = $config->getApiPassword();
        $fid = $config->getApiFid();

        if (!$login || !$password || !$fid) {
            return new Response($translator->trans('bitbag.shopware_dpd_app.order.config_not_found'));
        }

        $api = new Api($login, $password, $fid);
        $api->setSandboxMode(true);

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
