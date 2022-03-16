<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Controller;

use BitBag\ShopwareDpdApp\AppSystem\Exception\ShopNotFoundException;
use BitBag\ShopwareDpdApp\Entity\Config;
use BitBag\ShopwareDpdApp\Entity\ShopInterface;
use BitBag\ShopwareDpdApp\Form\Type\ConfigType;
use BitBag\ShopwareDpdApp\Repository\ConfigRepositoryInterface;
use BitBag\ShopwareDpdApp\Repository\ShopRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ConfigurationModuleController extends AbstractController
{
    private ConfigRepositoryInterface $configRepository;

    private EntityManagerInterface $entityManager;

    private ShopRepositoryInterface $shopRepository;

    public function __construct(
        ConfigRepositoryInterface $configRepository,
        EntityManagerInterface $entityManager,
        ShopRepositoryInterface $shopRepository
    ) {
        $this->configRepository = $configRepository;
        $this->entityManager = $entityManager;
        $this->shopRepository = $shopRepository;
    }

    public function __invoke(Request $request): Response
    {
        $shopId = $request->get('shop-id');

        /** @var ShopInterface $shop */
        $shop = $this->shopRepository->find($shopId);
        if (!$shop) {
            throw new ShopNotFoundException($shopId);
        }

        $config = $this->configRepository->findOneBy(['shop' => $shop]);
        if (!$config) {
            $config = new Config();
        }

        $form = $this->createForm(ConfigType::class, $config);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $config->setShop($shop);
            $this->entityManager->persist($config);
            $this->entityManager->flush();
        }

        return $this->render('configuration_module.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
