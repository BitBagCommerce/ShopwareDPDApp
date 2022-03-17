<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use BitBag\ShopwareDpdApp\AppSystem\Client\ClientInterface;

final class CreateCustomFieldFactory implements CreateCustomFieldFactoryInterface
{
    public function create(
        string $name,
        string $type,
        int $position,
        string $label,
        ClientInterface $client,
        ?string $customFieldSetId = null,
        ?array $customFieldSet = null
    ): void {
        $customFieldArr = [
            'name' => $name,
            'type' => $type,
            'position' => $position,
            'config' => [
                'type' => $type,
                'label' => ['en-GB' => $label],
                'helpText' => [],
                'placeholder' => [],
                'componentName' => 'sw-field',
                'customFieldType' => $type,
                'customFieldPosition' => $position,
            ],
        ];

        if ($customFieldSetId) {
            $customFieldArr['customFieldSetId'] = $customFieldSetId;
        } elseif ($customFieldSet) {
            $customFieldArr['customFieldSet'] = $customFieldSet;
        }

        if ('int' === $type) {
            $customFieldArr['config']['type'] = 'number';
            $customFieldArr['config']['numberType'] = $type;
            $customFieldArr['config']['customFieldType'] = 'number';
        }

        $client->createEntity('custom-field', $customFieldArr);
    }
}
