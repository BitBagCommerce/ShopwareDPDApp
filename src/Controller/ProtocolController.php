<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Controller;

use BitBag\ShopwareAppSystemBundle\Model\Action\ActionInterface;
use BitBag\ShopwareAppSystemBundle\Model\Feedback\NewTab;
use BitBag\ShopwareAppSystemBundle\Response\FeedbackResponse;
use BitBag\ShopwareDpdApp\Entity\ConfigInterface;
use BitBag\ShopwareDpdApp\Exception\ErrorNotificationException;
use BitBag\ShopwareDpdApp\Exception\PackageException;
use BitBag\ShopwareDpdApp\Factory\FeedbackResponseFactoryInterface;
use BitBag\ShopwareDpdApp\Repository\ConfigRepositoryInterface;
use BitBag\ShopwareDpdApp\Repository\PackageRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use T3ko\Dpd\Api;
use T3ko\Dpd\Request\GenerateProtocolRequest;

final class ProtocolController extends AbstractController
{
    private PackageRepositoryInterface $packageRepository;

    private FeedbackResponseFactoryInterface $feedbackResponseFactory;

    private ConfigRepositoryInterface $configRepository;

    public function __construct(
        PackageRepositoryInterface $packageRepository,
        FeedbackResponseFactoryInterface $feedbackResponseFactory,
        ConfigRepositoryInterface $configRepository
    ) {
        $this->packageRepository = $packageRepository;
        $this->feedbackResponseFactory = $feedbackResponseFactory;
        $this->configRepository = $configRepository;
    }

    public function getProtocol(ActionInterface $action): Response
    {
        $orderId = $action->getData()->getIds()[0] ?? '';

        try {
            $this->packageRepository->getByOrderId($orderId);
        } catch (PackageException $e) {
            return $this->feedbackResponseFactory->returnError($e->getMessage());
        }

        $redirectUrl = $this->generateUrl(
            'show_protocol',
            ['orderId' => $orderId],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new FeedbackResponse(new NewTab($redirectUrl));
    }

    public function showProtocol(Request $request): Response
    {
        $data = $request->query->all();
        $orderId = $data['orderId'] ?? '';
        $shopId = $data['shop-id'] ?? '';

        try {
            $config = $this->configRepository->getByShopId($shopId);
        } catch (ErrorNotificationException $e) {
            return $this->feedbackResponseFactory->returnError($e->getMessage());
        }

        $api = new Api(
            $config->getApiLogin(),
            $config->getApiPassword(),
            $config->getApiFid()
        );
        $api->setSandboxMode(ConfigInterface::SANDBOX_ENVIRONMENT === $config->getApiEnvironment());

        try {
            $package = $this->packageRepository->getByOrderId($orderId);
        } catch (PackageException $e) {
            return $this->feedbackResponseFactory->returnError($e->getMessage());
        }

        $protocolRequest = GenerateProtocolRequest::fromWaybills([$package->getWaybill()]);
        $protocolResponse = $api->generateProtocol($protocolRequest);

        $filename = sprintf('filename="protocol_%s.pdf"', 'order_' . $orderId);

        $response = new Response($protocolResponse->getFileContent());
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Content-Disposition', $filename);

        return $response;
    }
}
