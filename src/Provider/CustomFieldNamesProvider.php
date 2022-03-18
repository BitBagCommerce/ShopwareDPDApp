<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Provider;

final class CustomFieldNamesProvider implements CustomFieldNamesProviderInterface
{
    public function getFields(): array
    {
        return [
            [
                'name' => 'height',
                'label' => 'Height',
                'type' => 'int',
            ],
            [
                'name' => 'width',
                'label' => 'Width',
                'type' => 'int',
            ],
            [
                'name' => 'depth',
                'label' => 'Depth',
                'type' => 'int',
            ],
            [
                'name' => 'countryCode',
                'label' => 'Sender country code',
                'type' => 'text',
            ],
        ];
    }
}
