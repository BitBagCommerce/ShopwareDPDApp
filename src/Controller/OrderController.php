<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Controller;

use BitBag\ShopwareAppSystemBundle\Model\Action\ActionInterface;
use BitBag\ShopwareAppSystemBundle\Repository\ShopRepositoryInterface;
use Exception;
use JsonException;
use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Criteria;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;
use Vin\ShopwareSdk\Data\Entity\OrderLineItem\OrderLineItemEntity;
use Vin\ShopwareSdk\Data\Entity\Product\ProductEntity;
use Vin\ShopwareSdk\Data\Filter\EqualsFilter;
use Vin\ShopwareSdk\Repository\RepositoryInterface;

final class OrderController
{
    private ShopRepositoryInterface $shopRepository;

    private RepositoryInterface $orderRepository;

    private RepositoryInterface $productRepository;

    private TranslatorInterface $translator;

    public function __construct(
        ShopRepositoryInterface $shopRepository,
        RepositoryInterface $orderRepository,
        RepositoryInterface $productRepository,
        TranslatorInterface $translator
    ) {
        $this->shopRepository = $shopRepository;
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->translator = $translator;
    }

    /**
     * @throws Exception
     */
    public function __invoke(ActionInterface $action, Request $request, Context $context): Response
    {
        $this->checkSignature($request);

        $data = json_decode($request->getContent(), true, 512, \JSON_THROW_ON_ERROR);

        $shopId = $data['source']['shopId'];

        $orderId = $data['data']['ids'][0];

        $orderCriteria = new Criteria();
        $orderCriteria->addFilter(new EqualsFilter('id', $orderId));
        $orderCriteria->addAssociations(['lineItems.product', 'deliveries']);

        $searchOrder = $this->orderRepository->search($orderCriteria, $context);

        /** @var OrderEntity $order */
        $order = $searchOrder->first();

        $lineItems = $order->lineItems?->getElements();

        if (null === $lineItems) {
            $lineItems = [];
        }

        $totalWeight = 0;

        $products = array_map(fn (OrderLineItemEntity $item) => $item->product, $lineItems);
        $products = array_filter($products);
        $parentIds = array_filter($products, fn (ProductEntity $product) => null !== $product->parentId);

        $searchParentProductsCriteria = (new Criteria())
            ->setIds(array_column($parentIds, 'parentId'));

        $searchParentProducts = $this->productRepository->search($searchParentProductsCriteria, $context);

        $parentProducts = $searchParentProducts->entities->getElements();

        /**
         * @var OrderLineItemEntity $item
         */
        foreach ($lineItems as $item) {
            $product = $item->product;
            $productWeight = 0;

            if (null !== $product) {
                $parentId = $product->parentId;
                $productWeight = $product->weight;

                if (null !== $parentId && isset($parentProducts[$parentId])) {
                    /** @var ProductEntity $mainProduct */
                    $mainProduct = $parentProducts[$parentId];

                    $productWeight = $mainProduct->weight;
                }
            }

            $totalWeight += $item->quantity * $productWeight;
        }

        if (0.0 === $totalWeight) {
            $response = [
                'actionType' => 'notification',
                'payload' => [
                    'status' => 'error',
                    'message' => $this->translator->trans('bitbag.shopware_dpd_app.order.nullWeight'),
                ],
            ];

            return $this->sign($response, $shopId);
        }

        $response = [
            'actionType' => 'notification',
            'payload' => [
                'status' => 'success',
                'message' => 'Order controller works!',
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
        $requestContent = json_decode($request->getContent(), true, 512, \JSON_THROW_ON_ERROR);
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
