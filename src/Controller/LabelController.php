<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Controller;

use BitBag\ShopwareAppSystemBundle\Model\Action\ActionInterface;
use BitBag\ShopwareAppSystemBundle\Model\Feedback\NewTab;
use BitBag\ShopwareAppSystemBundle\Response\FeedbackResponse;
use BitBag\ShopwareDpdApp\Exception\ErrorNotificationException;
use BitBag\ShopwareDpdApp\Exception\PackageException;
use BitBag\ShopwareDpdApp\Factory\FeedbackResponseFactoryInterface;
use BitBag\ShopwareDpdApp\Repository\PackageRepositoryInterface;
use BitBag\ShopwareDpdApp\Resolver\ApiClientResolverInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use T3ko\Dpd\Request\GenerateLabelsRequest;

final class LabelController extends AbstractController
{
    private PackageRepositoryInterface $packageRepository;

    private FeedbackResponseFactoryInterface $feedbackResponseFactory;

    private ApiClientResolverInterface $apiClientResolver;

    public function __construct(
        PackageRepositoryInterface $packageRepository,
        FeedbackResponseFactoryInterface $feedbackResponseFactory,
        ApiClientResolverInterface $apiClientResolver
    ) {
        $this->packageRepository = $packageRepository;
        $this->feedbackResponseFactory = $feedbackResponseFactory;
        $this->apiClientResolver = $apiClientResolver;
    }

    public function getLabel(ActionInterface $action): Response
    {
        $orderId = $action->getData()->getIds()[0] ?? '';

        $package = $this->packageRepository->findByOrderId($orderId);

        if (null === $package) {
            return $this->feedbackResponseFactory->createError('bitbag.shopware_dpd_app.package.not_found');
        }

        $redirectUrl = $this->generateUrl(
            'show_label',
            ['orderId' => $orderId],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new FeedbackResponse(new NewTab($redirectUrl));
    }

    public function showLabel(Request $request): Response
    {
        $data = $request->query->all();
        $orderId = $data['orderId'] ?? '';
        $shopId = $data['shop-id'] ?? '';

        try {
            $api = $this->apiClientResolver->getClient($shopId);
        } catch (ErrorNotificationException $e) {
            return $this->feedbackResponseFactory->createError($e->getMessage());
        }

        try {
            $package = $this->packageRepository->getByOrderId($orderId);
        } catch (PackageException $e) {
            return $this->feedbackResponseFactory->createError($e->getMessage());
        }

        $labelRequest = GenerateLabelsRequest::fromWaybills([$package->getWaybill()]);
        $labelResponse = $api->generateLabels($labelRequest);

        $filename = sprintf('filename="label_%s.pdf"', 'order_' . $orderId);

        $response = new Response($labelResponse->getFileContent());
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Content-Disposition', $filename);

        return $response;
    }
}
