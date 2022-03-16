<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\Controller;

use BitBag\ShopwareAppSkeleton\Entity\ConfigInterface;
use BitBag\ShopwareAppSkeleton\Repository\ConfigRepositoryInterface;
use BitBag\ShopwareAppSkeleton\Repository\OrderRepositoryInterface;
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
        $order = $this->orderRepository->findByOrderId($orderId);
        if (!$order) {
            return new Response('Not found order');
        }

        if (!$order->getParcelId()) {
            return new Response('Not found parcelId');
        }

        /** @var ConfigInterface $config */
        $config = $this->configRepository->findByShopId($order->getShopId());

        $login = $config->getApiLogin();
        $password = $config->getApiPassword();
        $fid = $config->getApiFid();

        if (!$login || !$password || !$fid) {
            return new Response($this->translator->trans('bitbag.shopware_dpd_app.order.config_not_found'));
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
