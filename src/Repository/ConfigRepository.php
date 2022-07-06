<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Repository;

use BitBag\ShopwareDpdApp\Entity\Config;
use BitBag\ShopwareDpdApp\Entity\ConfigInterface;
use BitBag\ShopwareDpdApp\Exception\ErrorNotificationException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

final class ConfigRepository extends ServiceEntityRepository implements ConfigRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Config::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Config $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Config $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getByShopIdAndSalesChannelId(string $shopId, string $salesChannelId): ConfigInterface
    {
        $config = $this->getByShopIdAndSalesChannelIdQueryBuilder($shopId, $salesChannelId)
                       ->getQuery()
                       ->getOneOrNullResult();

        if (null === $config) {
            throw new ErrorNotificationException('bitbag.shopware_dpd_app.config.credentials_data_not_found');
        }

        return $config;
    }

    public function findByShopIdAndSalesChannelId(string $shopId, string $salesChannelId): ?ConfigInterface
    {
        return $this->getByShopIdAndSalesChannelIdQueryBuilder($shopId, $salesChannelId)
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    private function getByShopIdQueryBuilder(string $shopId): QueryBuilder
    {
        return $this->createQueryBuilder('c')
                    ->leftJoin('c.shop', 'shop')
                    ->where('shop.shopId = :shopId')
                    ->setParameter('shopId', $shopId);
    }

    private function getByShopIdAndSalesChannelIdQueryBuilder(string $shopId, string $salesChannelId): QueryBuilder
    {
        return $this->getByShopIdQueryBuilder($shopId)
                    ->andWhere('c.salesChannelId = :salesChannelId')
                    ->setParameter('salesChannelId', $salesChannelId);
    }
}
