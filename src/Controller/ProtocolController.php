<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Controller;

use BitBag\ShopwareAppSystemBundle\Exception\ShopNotFoundException;
use BitBag\ShopwareAppSystemBundle\Model\Action\ActionInterface;
use BitBag\ShopwareAppSystemBundle\Model\Feedback\NewTab;
use BitBag\ShopwareAppSystemBundle\Response\FeedbackResponse;
use BitBag\ShopwareDpdApp\Exception\ErrorNotificationException;
use BitBag\ShopwareDpdApp\Exception\Order\OrderException;
use BitBag\ShopwareDpdApp\Exception\PackageException;
use BitBag\ShopwareDpdApp\Factory\CreateContextFactoryInterface;
use BitBag\ShopwareDpdApp\Factory\FeedbackResponseFactoryInterface;
use BitBag\ShopwareDpdApp\Finder\OrderFinderInterface;
use BitBag\ShopwareDpdApp\Repository\PackageRepositoryInterface;
use BitBag\ShopwareDpdApp\Resolver\ApiClientResolverInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use T3ko\Dpd\Request\GenerateProtocolRequest;

final class ProtocolController extends AbstractController
{
    private PackageRepositoryInterface $packageRepository;

    private FeedbackResponseFactoryInterface $feedbackResponseFactory;

    private ApiClientResolverInterface $apiClientResolver;

    private OrderFinderInterface $orderFinder;

    private CreateContextFactoryInterface $createContextFactory;

    public function __construct(
        PackageRepositoryInterface $packageRepository,
        FeedbackResponseFactoryInterface $feedbackResponseFactory,
        ApiClientResolverInterface $apiClientResolver,
        OrderFinderInterface $orderFinder,
        CreateContextFactoryInterface $createContextFactory
    ) {
        $this->packageRepository = $packageRepository;
        $this->feedbackResponseFactory = $feedbackResponseFactory;
        $this->apiClientResolver = $apiClientResolver;
        $this->orderFinder = $orderFinder;
        $this->createContextFactory = $createContextFactory;
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
            $context = $this->createContextFactory->createByShopId($shopId);
        } catch (UnauthorizedHttpException | ShopNotFoundException $e) {
            return $this->feedbackResponseFactory->createError($e->getMessage());
        }

        try {
            $salesChannelId = $this->orderFinder->getSalesChannelIdByOrderId($orderId, $context);
        } catch (OrderException $e) {
            return $this->feedbackResponseFactory->createError($e->getMessage());
        }

        try {
            $api = $this->apiClientResolver->getApi($shopId, $salesChannelId);
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
