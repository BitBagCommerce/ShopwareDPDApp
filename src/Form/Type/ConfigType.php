<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Form\Type;

use BitBag\ShopwareDpdApp\Api\WebClientInterface;
use BitBag\ShopwareDpdApp\Entity\Config;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('apiLogin', TextType::class, [
                'label' => 'bitbag.shopware_dpd_app.config.apiLogin',
                'required' => true,
            ])
            ->add('apiPassword', TextType::class, [
                'label' => 'bitbag.shopware_dpd_app.config.apiPassword',
                'required' => true,
            ])
            ->add('apiFid', TextType::class, [
                'label' => 'bitbag.shopware_dpd_app.config.apiFid',
                'required' => true,
            ])
            ->add('apiEnvironment', ChoiceType::class, [
                'label' => 'bitbag.shopware_dpd_app.config.apiEnvironment',
                'required' => true,
                'choices' => [
                    'bitbag.shopware_dpd_app.config.productionEnvironment' => WebClientInterface::PRODUCTION_ENVIRONMENT,
                    'bitbag.shopware_dpd_app.config.sandboxEnvironment' => WebClientInterface::SANDBOX_ENVIRONMENT,
                ],
            ])
            ->add('senderFirstLastName', TextType::class, [
                'label' => 'bitbag.shopware_dpd_app.config.senderFirstLastName',
                'required' => true,
            ])
            ->add('senderStreet', TextType::class, [
                'label' => 'bitbag.shopware_dpd_app.config.senderStreet',
                'required' => true,
            ])
            ->add('senderZipCode', TextType::class, [
                'label' => 'bitbag.shopware_dpd_app.config.senderZipCode',
                'required' => true,
            ])
            ->add('senderCity', TextType::class, [
                'label' => 'bitbag.shopware_dpd_app.config.senderCity',
                'required' => true,
            ])
            ->add('senderPhoneNumber', TextType::class, [
                'label' => 'bitbag.shopware_dpd_app.config.senderPhoneNumber',
                'required' => true,
            ])
            ->add('senderLocale', TextType::class, [
                'label' => 'bitbag.shopware_dpd_app.config.senderLocale',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Config::class,
        ]);
    }

    public function getName(): string
    {
        return 'config';
    }
}
