<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Tests\Factory;

use BitBag\ShopwareDpdApp\Exception\Order\OrderException;
use BitBag\ShopwareDpdApp\Factory\ReceiverFactory;
use BitBag\ShopwareDpdApp\Provider\Defaults;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use T3ko\Dpd\Objects\Receiver;
use Vin\ShopwareSdk\Data\Entity\Order\OrderEntity;
use Vin\ShopwareSdk\Data\Entity\OrderAddress\OrderAddressCollection;
use Vin\ShopwareSdk\Data\Entity\OrderAddress\OrderAddressEntity;

final class ReceiverFactoryTest extends WebTestCase
{
    public function testCreate(): void
    {
        $receiverFactory = new ReceiverFactory();

        $address = new OrderAddressEntity();
        $address->phoneNumber = '123-123-123';
        $address->firstName = 'Jan';
        $address->lastName = 'Kowalski';
        $address->street = 'Jasna 4';
        $address->zipcode = '12-123';
        $address->city = 'Wrocław';

        $order = new OrderEntity();
        $order->addresses = new OrderAddressCollection([$address]);

        self::assertEquals(
            new Receiver(
                '123-123-123',
                'Jan Kowalski',
                'Jasna 4',
                '12-123',
                'Wrocław',
                Defaults::CURRENCY_CODE
            ),
            $receiverFactory->create($order)
        );
    }

    public function testAddressNotFoundException(): void
    {
        $receiverFactory = new ReceiverFactory();

        $this->expectException(OrderException::class);
        $this->expectExceptionMessage('bitbag.shopware_dpd_app.order.shipping_address_not_found');

        $receiverFactory->create(new OrderEntity());
    }

    public function testAddressInvalidException(): void
    {
        $receiverFactory = new ReceiverFactory();

        $address = new OrderAddressEntity();
        $address->phoneNumber = '123-123-123';
        $address->firstName = 'Jan';
        $address->lastName = 'Kowalski';
        $address->street = 'Jasna 4';
        $address->zipcode = '12-123';
        $address->city = null;

        $order = new OrderEntity();
        $order->addresses = new OrderAddressCollection([$address]);

        $this->expectException(OrderException::class);
        $this->expectExceptionMessage('bitbag.shopware_dpd_app.order.shipping_address_value_invalid');

        $receiverFactory->create($order);
    }
}
