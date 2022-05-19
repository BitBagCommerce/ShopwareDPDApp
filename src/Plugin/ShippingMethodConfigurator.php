<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Plugin;

use BitBag\ShopwareDpdApp\Exception\RuleNotFoundException;
use BitBag\ShopwareDpdApp\Factory\RulePackageFactoryInterface;
use BitBag\ShopwareDpdApp\Factory\ShippingMethodPayloadFactoryInterface;
use BitBag\ShopwareDpdApp\Finder\DeliveryTimeFinderInterface;
use BitBag\ShopwareDpdApp\Finder\PaymentMethodFinderInterface;
use BitBag\ShopwareDpdApp\Finder\RuleFinderInterface;
use BitBag\ShopwareDpdApp\Finder\ShippingMethodFinderInterface;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Repository\RepositoryInterface;

final class ShippingMethodConfigurator implements ShippingMethodConfiguratorInterface
{
    private ShippingMethodPayloadFactoryInterface $shippingMethodPayloadFactory;

    private ShippingMethodFinderInterface $shippingMethodFinder;

    private DeliveryTimeFinderInterface $deliveryTimeFinder;

    private RuleFinderInterface $ruleFinder;

    private RulePackageFactoryInterface $rulePackageFactory;

    private PaymentMethodFinderInterface $paymentMethodFinder;

    private RepositoryInterface $ruleRepository;

    private RepositoryInterface $shippingMethodRepository;

    public function __construct(
        ShippingMethodPayloadFactoryInterface $shippingMethodPayloadFactory,
        ShippingMethodFinderInterface $shippingMethodFinder,
        DeliveryTimeFinderInterface $deliveryTimeFinder,
        RuleFinderInterface $ruleFinder,
        RulePackageFactoryInterface $rulePackageFactory,
        PaymentMethodFinderInterface $paymentMethodFinder,
        RepositoryInterface $ruleRepository,
        RepositoryInterface $shippingMethodRepository
    ) {
        $this->shippingMethodPayloadFactory = $shippingMethodPayloadFactory;
        $this->shippingMethodFinder = $shippingMethodFinder;
        $this->deliveryTimeFinder = $deliveryTimeFinder;
        $this->ruleFinder = $ruleFinder;
        $this->rulePackageFactory = $rulePackageFactory;
        $this->paymentMethodFinder = $paymentMethodFinder;
        $this->ruleRepository = $ruleRepository;
        $this->shippingMethodRepository = $shippingMethodRepository;
    }

    public function createShippingMethod(Context $context): void
    {
        $ruleName = 'Cart >= 0';

        $shippingMethods = $this->shippingMethodFinder->find($context);

        if (0 < $shippingMethods->getTotal()) {
            return;
        }

        $deliveryTime = $this->deliveryTimeFinder->findDeliveryTimeByMinMax(1, 3, $context);

        $rule = $this->ruleFinder->find($ruleName, $context);

        if (0 === $rule->getTotal()) {
            $paymentMethodCahOnDelivery = $this->paymentMethodFinder->find(
                'Shopware\Core\Checkout\Payment\Cart\PaymentHandler\CashPayment',
                $context
            );

            $paymentMethodId = $paymentMethodCahOnDelivery->firstId();

            if (null !== $paymentMethodId) {
                $rule = $this->rulePackageFactory->create($ruleName, $paymentMethodId);

                $this->ruleRepository->create($rule, $context);

                $rule = $this->ruleFinder->find($ruleName, $context);
            }
        }

        if (0 === $rule->getTotal()) {
            throw new RuleNotFoundException('rule.notFound');
        }

        $ruleId = $rule->firstId();

        if (null === $ruleId) {
            throw new RuleNotFoundException('rule.notFound');
        }

        $shippingMethod = $this->shippingMethodPayloadFactory->create($ruleId, $context->currencyId, $deliveryTime);

        $this->shippingMethodRepository->create($shippingMethod, $context);
    }
}
