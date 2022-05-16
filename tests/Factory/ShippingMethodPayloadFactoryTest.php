<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Tests\Factory;

use BitBag\ShopwareDpdApp\Factory\ShippingMethodPayloadFactory;
use BitBag\ShopwareDpdApp\Factory\ShippingMethodPayloadFactoryInterface;
use PHPUnit\Framework\TestCase;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Criteria;
use Vin\ShopwareSdk\Data\Uuid\Uuid;
use Vin\ShopwareSdk\Repository\Struct\IdSearchResult;

final class ShippingMethodPayloadFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $name = ShippingMethodPayloadFactoryInterface::SHIPPING_KEY;

        $context = $this->createMock(Context::class);

        $deliveryTimeId = Uuid::randomHex();

        $deliveryTime = new IdSearchResult(
            1,
            [$deliveryTimeId],
            new Criteria(),
            $context
        );

        $ruleId = Uuid::randomHex();

        $currencyId = Uuid::randomHex();

        $shippingMethodPayloadFactory = new ShippingMethodPayloadFactory();

        self::assertEquals(
            [
                'name' => $name,
                'active' => true,
                'description' => $name . ' shipping method',
                'taxType' => 'auto',
                'translated' => [
                    'name' => $name,
                ],
                'customFields' => [
                    'technical_name' => $name,
                ],
                'availabilityRuleId' => $ruleId,
                'prices' => [
                    [
                        'ruleId' => $ruleId,
                        'calculation' => 1,
                        'quantityStart' => 1,
                        'currencyPrice' => [
                            $currencyId => [
                                'net' => 0.0,
                                'gross' => 0.0,
                                'linked' => false,
                                'currencyId' => $currencyId,
                            ],
                        ],
                    ],
                ],
                'deliveryTimeId' => $deliveryTimeId,
            ],
            $shippingMethodPayloadFactory->create($ruleId, $deliveryTime, $currencyId)
        );
    }
}
