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
use BitBag\ShopwareDpdApp\Finder\OrderFinderInterface;
use BitBag\ShopwareDpdApp\Finder\PackageFinderInterface;
use BitBag\ShopwareDpdApp\Form\Type\OrderCourierType;
use BitBag\ShopwareDpdApp\Persister\PackagePersisterInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Exception\AuthorizationFailedException;

final class CourierController extends AbstractController
{
    private ShopRepositoryInterface $shopRepository;

    private ContextFactoryInterface $contextFactory;

    private TranslatorInterface $translator;

    private OrderCourierServiceInterface $orderCourierService;

    private OrderFinderInterface $orderFinder;

    private PackageFinderInterface $packageFinder;

    private PackagePersisterInterface $packagePersister;

    public function __construct(
        ShopRepositoryInterface $shopRepository,
        ContextFactoryInterface $contextFactory,
        TranslatorInterface $translator,
        OrderCourierServiceInterface $orderCourierService,
        OrderFinderInterface $orderFinder,
        PackageFinderInterface $packageFinder,
        PackagePersisterInterface $packagePersister
    ) {
        $this->shopRepository = $shopRepository;
        $this->contextFactory = $contextFactory;
        $this->translator = $translator;
        $this->orderCourierService = $orderCourierService;
        $this->orderFinder = $orderFinder;
        $this->packageFinder = $packageFinder;
        $this->packagePersister = $packagePersister;
    }

    public function orderCourier(Request $request): Response
    {
        $orderCourier = new OrderCourier();
        $shopId = $request->query->get('shop-id', '');
        /** @var Shop $shop */
        $shop = $this->shopRepository->find($shopId);
        $context = $this->contextFactory->create($shop);

        if (null === $context) {
            throw new AuthorizationFailedException('bitbag.shopware_dpd_app.order_courier.authorization_failed_context');
        }

        $packages = $this->packageFinder->findOrdersWithoutOrderCourier();

        $form = $this->getForm($packages, $context, $orderCourier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

            try {
                $orderedCourierPackages = $this->orderCourierService->orderCourierByPackages(
                    $ordersFromData,
                    $packages,
                    $shopId,
                    $orderCourier,
                    $context
                );
            } catch (PackageException | OrderException $e) {
                $this->addFlash('error', $this->translator->trans($e->getMessage()));

                return $this->renderForm('order_courier/form-list.html.twig', [
                    'form' => $form,
                ]);
            }

            $this->saveOrderCourierNumberInPackages($orderedCourierPackages);

            $this->addFlash('success', $this->translator->trans('bitbag.shopware_dpd_app.order_courier.courier_ordered'));

            $form = $this->getForm($packages, $context);
        }

        return $this->renderForm('order_courier/form-list.html.twig', [
            'form' => $form,
        ]);
    }

    private function getForm(
        array $packages,
        Context $context,
        ?OrderCourier $orderCourier = null
    ): FormInterface {
        $orders = $this->getOrdersForForm($packages, $context);

        return $this->createForm(OrderCourierType::class, $orderCourier ?? new OrderCourier(), [
            'orders' => $orders,
        ]);
    }

    private function getOrdersForForm(array $packages, Context $context): array
    {
        $packagesIds = array_map(static fn (Package $package) => $package->getOrderId(), $packages);

        return $this->orderFinder->getOrdersByPackagesIds($packagesIds, $context);
    }

    private function saveOrderCourierNumberInPackages(array $packagesData): void
    {
        foreach ($packagesData as $data) {
            $this->packagePersister->saveOrderNumber($data['package'], $data['orderCourierNumber']);
        }
    }
}
