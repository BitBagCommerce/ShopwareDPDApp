<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Controller;

use BitBag\ShopwareAppSystemBundle\Model\Action\ActionInterface;
use BitBag\ShopwareAppSystemBundle\Model\Feedback\NewTab;
use BitBag\ShopwareAppSystemBundle\Response\FeedbackResponse;
use BitBag\ShopwareDpdApp\Api\ApiServiceInterface;
use BitBag\ShopwareDpdApp\Exception\ErrorNotificationException;
use BitBag\ShopwareDpdApp\Exception\PackageException;
use BitBag\ShopwareDpdApp\Factory\FeedbackResponseFactoryInterface;
use BitBag\ShopwareDpdApp\Repository\PackageRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use T3ko\Dpd\Request\GenerateProtocolRequest;

final class ProtocolController extends AbstractController
{
    private PackageRepositoryInterface $packageRepository;

    private FeedbackResponseFactoryInterface $feedbackResponseFactory;

    private ApiServiceInterface $apiService;

    public function __construct(
        PackageRepositoryInterface $packageRepository,
        FeedbackResponseFactoryInterface $feedbackResponseFactory,
        ApiServiceInterface $apiService
    ) {
        $this->packageRepository = $packageRepository;
        $this->feedbackResponseFactory = $feedbackResponseFactory;
        $this->apiService = $apiService;
    }

    public function getProtocol(ActionInterface $action): Response
    {
        $orderId = $action->getData()->getIds()[0] ?? '';

        try {
            $this->packageRepository->getByOrderId($orderId);
        } catch (PackageException $e) {
            return $this->feedbackResponseFactory->createError($e->getMessage());
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
            $api = $this->apiService->getApi($shopId);
        } catch (ErrorNotificationException $e) {
            return $this->feedbackResponseFactory->createError($e->getMessage());
        }

        $package = $this->packageRepository->findByOrderId($orderId);

        if (null === $package) {
            return $this->feedbackResponseFactory->createError('bitbag.shopware_dpd_app.package.not_found');
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
