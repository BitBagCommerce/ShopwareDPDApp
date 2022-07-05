<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Tests\Factory;

use BitBag\ShopwareDpdApp\Entity\Config;
use BitBag\ShopwareDpdApp\Entity\ConfigInterface;
use BitBag\ShopwareDpdApp\Factory\DpdSenderFactory;
use BitBag\ShopwareDpdApp\Provider\Defaults;
use BitBag\ShopwareDpdApp\Repository\ConfigRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use T3ko\Dpd\Objects\Sender;
use Vin\ShopwareSdk\Data\Uuid\Uuid;

final class DpdSenderFactoryTest extends WebTestCase
{
    public function testCreate(): void
    {
        $config = $this->getConfig();

        $configRepositoryInterface = $this->createMock(ConfigRepositoryInterface::class);
        $configRepositoryInterface->expects(self::once())
            ->method('getByShopIdAndSalesChannelId')
            ->willReturn($config);

        $dpdSenderFactory = new DpdSenderFactory($configRepositoryInterface);

        self::assertEquals(
            new Sender(
                $config->getApiFid(),
                $config->getSenderPhoneNumber(),
                $config->getSenderFirstLastName(),
                $config->getSenderStreet(),
                $config->getSenderZipCode(),
                $config->getSenderCity(),
                $config->getSenderLocale()
            ),
            $dpdSenderFactory->create(Uuid::randomHex())
        );
    }

    private function getConfig(): ConfigInterface
    {
        $config = new Config();
        $config->setApiFid('simple_fid');
        $config->setApiLogin('simple_login');
        $config->setApiPassword('s3cr3t');
        $config->setApiEnvironment(ConfigInterface::SANDBOX_ENVIRONMENT);
        $config->setSenderFirstLastName('Jan Kowalski');
        $config->setSenderPhoneNumber('123-123-123');
        $config->setSenderStreet('Jasna 4');
        $config->setSenderZipCode('12-123');
        $config->setSenderCity('Wrocław');
        $config->setSenderLocale(Defaults::CURRENCY_CODE);

        return $config;
    }
}
