<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Finder;

use BitBag\ShopwareDpdApp\Exception\Order\OrderException;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Criteria;
use Vin\ShopwareSdk\Data\Entity\Order\OrderCollection;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;
use Vin\ShopwareSdk\Data\FieldSorting;
use Vin\ShopwareSdk\Data\Filter\EqualsAnyFilter;
use Vin\ShopwareSdk\Data\Filter\EqualsFilter;
use Vin\ShopwareSdk\Repository\RepositoryInterface;

final class OrderFinder implements OrderFinderInterface
{
    private RepositoryInterface $orderRepository;

    public function __construct(RepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
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

    public function getOrdersByPackagesIds(array $packagesIds, Context $context): OrderCollection
    {
        if ([] === $packagesIds) {
            return new OrderCollection();
        }

        $ordersCriteria = (new Criteria())
            ->addFilter(new EqualsAnyFilter('id', $packagesIds))
            ->addAssociations([
                'billingAddress',
                'addresses',
                'lineItems.product',
            ])
            ->addSorting(new FieldSorting('orderNumber', 'DESC'));

        /** @var OrderCollection $collection */
        $collection = $this->orderRepository->search($ordersCriteria, $context)->getEntities();

        return $collection;
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
        $order = $this->getWithAssociations($orderId, $context);

        return $this->getSalesChannelIdByOrder($order, $context);
    }
}
