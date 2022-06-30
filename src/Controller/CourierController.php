<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Controller;

use BitBag\ShopwareAppSystemBundle\Entity\Shop;
use BitBag\ShopwareAppSystemBundle\Factory\Context\ContextFactoryInterface;
use BitBag\ShopwareAppSystemBundle\Repository\ShopRepositoryInterface;
use BitBag\ShopwareDpdApp\Api\OrderCourierServiceInterface;
use BitBag\ShopwareDpdApp\Entity\OrderCourier;
use BitBag\ShopwareDpdApp\Entity\Package;
use BitBag\ShopwareDpdApp\Exception\Order\OrderException;
use BitBag\ShopwareDpdApp\Exception\PackageException;
use BitBag\ShopwareDpdApp\Form\Type\OrderCourierType;
use BitBag\ShopwareDpdApp\Repository\PackageRepositoryInterface;
use BitBag\ShopwareDpdApp\Resolver\ApiClientResolverInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Criteria;
use Vin\ShopwareSdk\Data\FieldSorting;
use Vin\ShopwareSdk\Data\Filter\EqualsAnyFilter;
use Vin\ShopwareSdk\Repository\RepositoryInterface;

final class CourierController extends AbstractController
{
    private PackageRepositoryInterface $packageRepository;

    private ApiClientResolverInterface $apiClientResolver;

    private RepositoryInterface $orderRepository;

    private ShopRepositoryInterface $shopRepository;

    private ContextFactoryInterface $contextFactory;

    private TranslatorInterface $translator;

    private OrderCourierServiceInterface $orderCourierService;

    public function __construct(
        PackageRepositoryInterface $packageRepository,
        ApiClientResolverInterface $apiClientResolver,
        RepositoryInterface $orderRepository,
        ShopRepositoryInterface $shopRepository,
        ContextFactoryInterface $contextFactory,
        TranslatorInterface $translator,
        OrderCourierServiceInterface $orderCourierService
    ) {
        $this->packageRepository = $packageRepository;
        $this->apiClientResolver = $apiClientResolver;
        $this->orderRepository = $orderRepository;
        $this->shopRepository = $shopRepository;
        $this->contextFactory = $contextFactory;
        $this->translator = $translator;
        $this->orderCourierService = $orderCourierService;
    }

    public function orderCourier(Request $request): Response
    {
        $orderCourier = new OrderCourier();

        $shopId = $request->query->get('shop-id', '');

        /** @var Shop $shop */
        $shop = $this->shopRepository->find($shopId);

        /** @var Context $context */
        $context = $this->contextFactory->create($shop);

        $packages = $this->getPackages();
        $orders = $this->getOrdersForForm($packages, $context);

        $form = $this->createForm(OrderCourierType::class, $orderCourier, [
            'orders' => $orders,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var OrderCourier $formData */
            $formData = $form->getData();
            $ordersFromData = $form->get('orders')->getData();

            if (null === $form->get('pickupTimeFrom')->getData() ||
                null === $form->get('pickupTimeTo')->getData() ||
                [] === $ordersFromData
            ) {
                $this->addFlash('error', $this->translator->trans('bitbag.shopware_dpd_app.order_courier.form_data_invalid'));

                return $this->renderForm('order_courier/form-list.html.twig', [
                    'form' => $form,
                ]);
            }

            $selectedOrdersIds = [];

            foreach ($ordersFromData as $order) {
                $selectedOrdersIds[] = $order->id;
            }

            $packagesBySelectedOrders = $this->packageRepository->findBy(['orderId' => $selectedOrdersIds]);

            $packagesBySelectedOrdersArr = [];

            /** @var Package $package */
            foreach ($packagesBySelectedOrders as $package) {
                $packageId = $package->getId();

                if (null !== $packageId) {
                    $packagesBySelectedOrdersArr[$packageId] = $package;
                }
            }

            try {
                $this->orderCourierService->orderCourierByPackages(
                    $ordersFromData,
                    $packages,
                    $packagesBySelectedOrdersArr,
                    $shopId,
                    $formData,
                    $context
                );
            } catch (PackageException | OrderException $e) {
                $this->addFlash('error', $this->translator->trans($e->getMessage()));

                return $this->renderForm('order_courier/form-list.html.twig', [
                    'form' => $form,
                ]);
            }

            $this->addFlash('success', $this->translator->trans('bitbag.shopware_dpd_app.order_courier.courier_ordered'));

            $packages = $this->getPackages();
            $orders = $this->getOrdersForForm($packages, $context);

            $form = $this->createForm(OrderCourierType::class, new OrderCourier(), [
                'orders' => $orders,
            ]);
        }

        return $this->renderForm('order_courier/form-list.html.twig', [
            'form' => $form,
        ]);
    }

    private function getPackages(): array
    {
        $packagesIdsArr = $this->packageRepository->findOrdersIdsWithoutOrderCourier();

        $packages = [];

        foreach ($packagesIdsArr as $package) {
            $packages[$package['id']] = $package;
        }

        return $packages;
    }

    private function getOrdersForForm(array $packages, Context $context): array
    {
        $packagesIds = array_column($packages, 'orderId');

        $orders = [];

        if ([] !== $packagesIds) {
            $ordersCriteria = (new Criteria())
                ->addFilter(new EqualsAnyFilter('id', $packagesIds))
                ->addAssociations([
                    'billingAddress',
                    'addresses',
                    'lineItems.product',
                ])
                ->addSorting(new FieldSorting('orderNumber', 'DESC'));

            $ordersSearch = $this->orderRepository->search($ordersCriteria, $context);

            $orders = $ordersSearch->getEntities()->getElements();
        }

        return $orders;
    }
}
