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
                'label' => 'bitbag.shopware_dpd_app.config.api_login',
                'required' => true,
            ])
            ->add('apiPassword', TextType::class, [
                'label' => 'bitbag.shopware_dpd_app.config.api_password',
                'required' => true,
            ])
            ->add('apiFid', TextType::class, [
                'label' => 'bitbag.shopware_dpd_app.config.api_fid',
                'required' => true,
            ])
            ->add('apiEnvironment', ChoiceType::class, [
                'label' => 'bitbag.shopware_dpd_app.config.api_environment',
                'required' => true,
                'choices' => [
                    'bitbag.shopware_dpd_app.config.production_environment' => WebClientInterface::PRODUCTION_ENVIRONMENT,
                    'bitbag.shopware_dpd_app.config.sandbox_environment' => WebClientInterface::SANDBOX_ENVIRONMENT,
                ],
            ])
            ->add('senderFirstLastName', TextType::class, [
                'label' => 'bitbag.shopware_dpd_app.config.sender_first_last_name',
                'required' => true,
            ])
            ->add('senderStreet', TextType::class, [
                'label' => 'bitbag.shopware_dpd_app.config.sender_street',
                'required' => true,
            ])
            ->add('senderZipCode', TextType::class, [
                'label' => 'bitbag.shopware_dpd_app.config.sender_zip_code',
                'required' => true,
            ])
            ->add('senderCity', TextType::class, [
                'label' => 'bitbag.shopware_dpd_app.config.sender_city',
                'required' => true,
            ])
            ->add('senderPhoneNumber', TextType::class, [
                'label' => 'bitbag.shopware_dpd_app.config.sender_phone_number',
                'required' => true,
            ])
            ->add('senderLocale', TextType::class, [
                'label' => 'bitbag.shopware_dpd_app.config.sender_locale',
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
