<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Form\Type;

use BitBag\ShopwareDpdApp\Entity\OrderCourier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

final class OrderCourierType extends AbstractType
{
    public const HOUR_MINUTE_REGEX = '/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/';

    public const HOUR_MINUTE_REGEX_INPUT = '([0-1]?[0-9]|2[0-3]):[0-5][0-9]';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('orders', ChoiceType::class, [
                'label' => 'bitbag.shopware_dpd_app.order_courier.orders',
                'required' => true,
                'choices' => $options['orders'],
                'mapped' => false,
                'multiple' => true,
                'choice_label' => fn (OrderEntity $order) => $this->renderLabelForOrder($order),
            ])
            ->add('pickupDate', TextType::class, [
                'label' => 'bitbag.shopware_dpd_app.order_courier.pickup_date',
                'required' => true,
                'data' => (new \DateTime())->format('Y-m-d'),
            ])
            ->add('pickupTimeFrom', TextType::class, [
                'label' => 'bitbag.shopware_dpd_app.order_courier.pickup_time_from',
                'required' => true,
                'attr' => [
                    'pattern' => self::HOUR_MINUTE_REGEX_INPUT,
                    'placeholder' => '8:00',
                ],
            ])
            ->add('pickupTimeTo', TextType::class, [
                'label' => 'bitbag.shopware_dpd_app.order_courier.pickup_time_to',
                'required' => true,
                'attr' => [
                    'pattern' => self::HOUR_MINUTE_REGEX_INPUT,
                    'placeholder' => '18:00',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OrderCourier::class,
            'orders' => [],
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }

    public function getName(): string
    {
        return 'order_courier';
    }

    private function renderLabelForOrder(OrderEntity $order): string
    {
        $billingAddress = $order->billingAddress;

        if (null === $billingAddress) {
            return '';
        }

        $firstName = $billingAddress->firstName;
        $lastName = $billingAddress->lastName;
        $orderNumber = $order->orderNumber;

        if (null === $firstName || null === $lastName || null === $orderNumber) {
            return '';
        }

        return $firstName . ' ' . $lastName . ' (' . $orderNumber . ')';
    }
}
