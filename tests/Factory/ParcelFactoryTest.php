<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Tests\Factory;

use BitBag\ShopwareDpdApp\Calculator\OrderWeightCalculatorInterface;
use BitBag\ShopwareDpdApp\Factory\ParcelFactory;
use BitBag\ShopwareDpdApp\Resolver\OrderCustomFieldsResolverInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use T3ko\Dpd\Objects\Parcel;
use Vin\ShopwareSdk\Data\Context;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;

final class ParcelFactoryTest extends WebTestCase
{
    public function testCreate(): void
    {
        $orderCustomFieldsResolver = $this->createMock(OrderCustomFieldsResolverInterface::class);
        $orderCustomFieldsResolver
            ->method('resolve')
            ->willReturn([
                'height' => 10,
                'width' => 10,
                'depth' => 10,
            ]);

        $orderWeightCalculator = $this->createMock(OrderWeightCalculatorInterface::class);
        $orderWeightCalculator
            ->method('calculate')
            ->willReturn(2.5);

        $context = $this->createMock(Context::class);

        $parcelFactory = new ParcelFactory(
            $orderCustomFieldsResolver,
            $orderWeightCalculator
        );

        self::assertEquals(
            new Parcel(10, 10, 10, 2.5),
            $parcelFactory->create(new OrderEntity(), $context)
        );
    }

    public function testParcelTooLarge(): void
    {
        $this->expectExceptionMessage('bitbag.shopware_dpd_app.package.too_large');

        $orderCustomFieldsResolver = $this->createMock(OrderCustomFieldsResolverInterface::class);
        $orderCustomFieldsResolver
            ->method('resolve')
            ->willReturn([
                'height' => 100,
                'width' => 100,
                'depth' => 100,
            ]);

        $orderWeightCalculator = $this->createMock(OrderWeightCalculatorInterface::class);
        $orderWeightCalculator
            ->method('calculate')
            ->willReturn(2.5);

        $context = $this->createMock(Context::class);

        $parcelFactory = new ParcelFactory(
            $orderCustomFieldsResolver,
            $orderWeightCalculator
        );

        self::assertEquals(
            new Parcel(100, 100, 100, 2.5),
            $parcelFactory->create(new OrderEntity(), $context)
        );
    }
}
