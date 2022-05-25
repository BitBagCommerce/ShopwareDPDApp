<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Repository;

use BitBag\ShopwareDpdApp\Entity\Config;
use BitBag\ShopwareDpdApp\Entity\ConfigInterface;
use BitBag\ShopwareDpdApp\Exception\ErrorNotificationException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
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

    public function getByShopId(string $shopId): ConfigInterface
    {
        $queryBuilder = $this->createQueryBuilder('c')
                             ->leftJoin('c.shop', 'shop')
                             ->where('shop.shopId = :shopId')
                             ->setParameter('shopId', $shopId);

        $config = $queryBuilder
            ->getQuery()
            ->getOneOrNullResult();

        if (null === $config) {
            throw new ErrorNotificationException('bitbag.shopware_dpd_app.config.credentials_data_not_found');
        }

        return $config;
    }
}
