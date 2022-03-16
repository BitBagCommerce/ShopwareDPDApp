<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('apiLogin', null, [
                'label' => 'bitbag.shopware_dpd_app.ui.apiLogin',
                'required' => true,
            ])
            ->add('apiPassword', null, [
                'label' => 'bitbag.shopware_dpd_app.ui.apiPassword',
                'required' => true,
            ])
            ->add('apiFid', null, [
                'label' => 'bitbag.shopware_dpd_app.ui.apiFid',
                'required' => true,
            ])
            ->add('senderFirstLastName', null, [
                'label' => 'bitbag.shopware_dpd_app.ui.senderFirstLastName',
                'required' => true,
            ])
            ->add('senderStreet', null, [
                'label' => 'bitbag.shopware_dpd_app.ui.senderStreet',
                'required' => true,
            ])
            ->add('senderZipCode', null, [
                'label' => 'bitbag.shopware_dpd_app.ui.senderZipCode',
                'required' => true,
            ])
            ->add('senderCity', null, [
                'label' => 'bitbag.shopware_dpd_app.ui.senderCity',
                'required' => true,
            ])
            ->add('senderPhoneNumber', null, [
                'label' => 'bitbag.shopware_dpd_app.ui.senderPhoneNumber',
                'required' => true,
            ])
            ->add('senderLocale', null, [
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
