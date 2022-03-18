<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

final class CreateCustomFieldSetFactory implements CreateCustomFieldSetFactoryInterface
{
    public function create(string $name, string $labelName, string $entityName): array
    {
        return [
            'name' => $name,
            'relations' => [
                [
                    'entityName' => $entityName,
                ],
            ],
            'config' => [
                'label' => ['en-GB' => $labelName],
                'translated' => true,
            ],
        ];
    }
}
