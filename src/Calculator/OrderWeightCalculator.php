<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Calculator;

use BitBag\ShopwareDpdApp\Exception\PackageException;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Criteria;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;
use Vin\ShopwareSdk\Data\Entity\OrderLineItem\OrderLineItemEntity;
use Vin\ShopwareSdk\Data\Entity\Product\ProductEntity;
use Vin\ShopwareSdk\Repository\RepositoryInterface;

final class OrderWeightCalculator implements OrderWeightCalculatorInterface
{
    private RepositoryInterface $productRepository;

    public function __construct(RepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function calculate(OrderEntity $order, Context $context): float
    {
        $totalWeight = 0.0;

        $lineItems = $order->lineItems?->getElements();

        if (null === $lineItems) {
            $lineItems = [];
        }

        $products = array_map(static fn (OrderLineItemEntity $item) => $item->product, $lineItems);
        $products = array_filter($products);
        $parentIds = array_filter($products, static fn (ProductEntity $product) => null !== $product->parentId);

        $searchParentProductsCriteria = (new Criteria())
            ->setIds(array_column($parentIds, 'parentId'));

        $searchParentProducts = $this->productRepository->search($searchParentProductsCriteria, $context);

        $parentProducts = $searchParentProducts->entities->getElements();

        foreach ($lineItems as $item) {
            $product = $item->product;
            $productWeight = 0.0;

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
            throw new PackageException('bitbag.shopware_dpd_app.order.null_weight');
        }

        return $totalWeight;
    }
}
