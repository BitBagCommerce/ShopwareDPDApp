<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Finder;

use BitBag\ShopwareDpdApp\Exception\Order\OrderException;
use BitBag\ShopwareDpdApp\Provider\NotificationProviderInterface;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Criteria;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;
use Vin\ShopwareSdk\Data\Filter\EqualsFilter;
use Vin\ShopwareSdk\Repository\RepositoryInterface;

final class OrderFinder implements OrderFinderInterface
{
    private RepositoryInterface $orderRepository;

    private NotificationProviderInterface $notificationProvider;

    public function __construct(
        RepositoryInterface $orderRepository,
        NotificationProviderInterface $notificationProvider
    ) {
        $this->orderRepository = $orderRepository;
        $this->notificationProvider = $notificationProvider;
    }

    public function getWithAssociations(?string $orderId, Context $context): OrderEntity
    {
        $orderCriteria = (new Criteria())->addFilter(new EqualsFilter('id', $orderId));
        $orderCriteria->addAssociations([
            'lineItems.product',
            'deliveries.shippingMethod',
            'addresses',
        ]);

        $searchOrder = $this->orderRepository->search($orderCriteria, $context);

        /** @var OrderEntity|null $order */
        $order = $searchOrder->first();

        if (null === $order) {
            throw new OrderException('bitbag.shopware_dpd_app.order.not_found');
        }

        return $order;
    }
}
