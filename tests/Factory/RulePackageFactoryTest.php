<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Tests\Factory;

use BitBag\ShopwareDpdApp\Factory\RulePackageFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Vin\ShopwareSdk\Data\Uuid\Uuid;

final class RulePackageFactoryTest extends WebTestCase
{
    public function testCreate(): void
    {
        $rulePackageFactory = new RulePackageFactory();

        $name = 'foo';

        $paymentMethodId = Uuid::randomHex();

        self::assertEquals(
            [
                'name' => $name,
                'priority' => 0,
                'conditions' => [
                    [
                        'type' => 'paymentMethod',
                        'value' => [
                            'paymentMethodIds' => [$paymentMethodId],
                            'operator' => '!=',
                        ],
                    ],
                ],
            ],
            $rulePackageFactory->create($name, $paymentMethodId)
        );
    }
}
