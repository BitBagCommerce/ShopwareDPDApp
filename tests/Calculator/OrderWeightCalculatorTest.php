<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Tests\Calculator;

use BitBag\ShopwareDpdApp\Calculator\OrderWeightCalculator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Criteria;
use Vin\ShopwareSdk\Data\Entity\EntityCollection;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;
use Vin\ShopwareSdk\Data\Entity\OrderLineItem\OrderLineItemCollection;
use Vin\ShopwareSdk\Data\Entity\OrderLineItem\OrderLineItemEntity;
use Vin\ShopwareSdk\Data\Entity\Product\ProductEntity;
use Vin\ShopwareSdk\Repository\RepositoryInterface;
use Vin\ShopwareSdk\Repository\Struct\AggregationResultCollection;
use Vin\ShopwareSdk\Repository\Struct\EntitySearchResult;
use Vin\ShopwareSdk\Repository\Struct\SearchResultMeta;

final class OrderWeightCalculatorTest extends WebTestCase
{
    public function testCalculate(): void
    {
        $context = $this->createMock(Context::class);

        $order = new OrderEntity();

        $product = new ProductEntity();
        $product->weight = 0.8;

        $orderLineItem = new OrderLineItemEntity();
        $orderLineItem->quantity = 1;
        $orderLineItem->product = $product;

        $product2 = new ProductEntity();
        $product2->weight = 0.45;

        $orderLineItem2 = new OrderLineItemEntity();
        $orderLineItem2->quantity = 1;
        $orderLineItem2->product = $product2;

        $order->lineItems = new OrderLineItemCollection([$orderLineItem, $orderLineItem2]);

        $productRepository = $this->createMock(RepositoryInterface::class);
        $productRepository
            ->method('search')
            ->willReturn(
                new EntitySearchResult(
                    'product',
                        new SearchResultMeta(1, 1),
                    new EntityCollection([$product]),
                    new AggregationResultCollection([]),
                    new Criteria(),
                    $context
                )
            );

        $orderWeightCalculator = new OrderWeightCalculator($productRepository);

        self::assertEquals(
            1.25,
            $orderWeightCalculator->calculate($order, $context)
        );
    }
}
