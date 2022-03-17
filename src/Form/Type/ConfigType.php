<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('apiLogin', TextType::class, [
                'label' => 'bitbag.shopware_dpd_app.ui.apiLogin',
                'required' => true,
            ])
            ->add('apiPassword', TextType::class, [
                'label' => 'bitbag.shopware_dpd_app.ui.apiPassword',
                'required' => true,
            ])
            ->add('apiFid', TextType::class, [
                'label' => 'bitbag.shopware_dpd_app.ui.apiFid',
                'required' => true,
            ])
            ->add('senderFirstLastName', TextType::class, [
                'label' => 'bitbag.shopware_dpd_app.ui.senderFirstLastName',
                'required' => true,
            ])
            ->add('senderStreet', TextType::class, [
                'label' => 'bitbag.shopware_dpd_app.ui.senderStreet',
                'required' => true,
            ])
            ->add('senderZipCode', TextType::class, [
                'label' => 'bitbag.shopware_dpd_app.ui.senderZipCode',
                'required' => true,
            ])
            ->add('senderCity', TextType::class, [
                'label' => 'bitbag.shopware_dpd_app.ui.senderCity',
                'required' => true,
            ])
            ->add('senderPhoneNumber', TextType::class, [
                'label' => 'bitbag.shopware_dpd_app.ui.senderPhoneNumber',
                'required' => true,
            ])
            ->add('senderLocale', TextType::class, [
                'label' => 'bitbag.shopware_dpd_app.ui.senderLocale',
                'required' => true,
            ])
        ;
    }

    public function getName(): string
    {
        return 'config';
    }
}
