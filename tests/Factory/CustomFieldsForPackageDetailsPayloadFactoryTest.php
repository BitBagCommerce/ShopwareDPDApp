<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Tests\Factory;

use BitBag\ShopwareDpdApp\Factory\CustomFieldsForPackageDetailsPayloadFactory;
use BitBag\ShopwareDpdApp\Factory\CustomFieldsForPackageDetailsPayloadFactoryInterface;
use PHPUnit\Framework\TestCase;

final class CustomFieldsForPackageDetailsPayloadFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $customFieldPrefix = CustomFieldsForPackageDetailsPayloadFactoryInterface::PACKAGE_DETAILS_KEY;

        $customFieldsForPackageDetailsPayload = new CustomFieldsForPackageDetailsPayloadFactory();

        self::assertEquals(
            [
                'name' => $customFieldPrefix,
                'config' => [
                    'label' => [
                        'en-GB' => 'Package details (DPD)',
                        'pl-PL' => 'Szczegóły paczki (DPD)',
                    ],
                    'translated' => true,
                    'technical_name' => $customFieldPrefix,
                ],
                'customFields' => [
                    [
                        'name' => $customFieldPrefix . '_insurance',
                        'label' => 'Insurance value (can be left empty)',
                        'type' => 'float',
                        'config' => [
                            'label' => [
                                'en-GB' => 'Insurance value (can be left empty)',
                                'pl-PL' => 'Wartość ubezpieczenia (może zostać puste)',
                            ],
                        ],
                    ],
                    [
                        'name' => $customFieldPrefix . '_height',
                        'label' => 'Height (cm)',
                        'type' => 'int',
                        'config' => [
                            'label' => [
                                'en-GB' => 'Height (cm)',
                                'pl-PL' => 'Wysokość (cm)',
                            ],
                        ],
                    ],
                    [
                        'name' => $customFieldPrefix . '_width',
                        'label' => 'Width (cm)',
                        'type' => 'int',
                        'config' => [
                            'label' => [
                                'en-GB' => 'Width (cm)',
                                'pl-PL' => 'Szerokość (cm)',
                            ],
                        ],
                    ],
                    [
                        'name' => $customFieldPrefix . '_depth',
                        'label' => 'Depth (cm)',
                        'type' => 'int',
                        'config' => [
                            'label' => [
                                'en-GB' => 'Depth (cm)',
                                'pl-PL' => 'Głębokość (cm)',
                            ],
                        ],
                    ],
                ],
                'relations' => [
                    [
                        'entityName' => 'order',
                    ],
                ],
            ],
            $customFieldsForPackageDetailsPayload->create()
        );
    }
}
