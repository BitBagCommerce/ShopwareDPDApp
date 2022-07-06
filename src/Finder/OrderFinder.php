<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Finder;

use BitBag\ShopwareDpdApp\Exception\Order\OrderException;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Criteria;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;
use Vin\ShopwareSdk\Data\Filter\EqualsFilter;
use Vin\ShopwareSdk\Repository\RepositoryInterface;

final class OrderFinder implements OrderFinderInterface
{
    private RepositoryInterface $orderRepository;

    private OrderFinderInterface $orderFinder;

    public function __construct(
        RepositoryInterface $orderRepository,
        OrderFinderInterface $orderFinder
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderFinder = $orderFinder;
    }

    public function getWithAssociations(?string $orderId, Context $context): OrderEntity
    {
        $orderCriteria = (new Criteria())->addFilter(new EqualsFilter('id', $orderId));
        $orderCriteria->addAssociations([
            'lineItems.product',
            'deliveries.shippingMethod',
            'addresses',
            'transactions',
            'transactions.paymentMethod',
        ]);

        $searchOrder = $this->orderRepository->search($orderCriteria, $context);

        /** @var OrderEntity|null $order */
        $order = $searchOrder->first();

        if (null === $order) {
            throw new OrderException('bitbag.shopware_dpd_app.order.not_found');
        }

        return $order;
    }

    public function getSalesChannelIdByOrder(OrderEntity $order, Context $context): string
    {
        $salesChannelId = $order->salesChannelId;

        if (null === $salesChannelId) {
            throw new OrderException('bitbag.shopware_dpd_app.order.sales_channel_id_not_found');
        }

        return $salesChannelId;
    }

    public function getSalesChannelIdByOrderId(string $orderId, Context $context): string
    {
        $order = $this->orderFinder->getWithAssociations($orderId, $context);

        return $this->getSalesChannelIdByOrder($order, $context);
    }
}
