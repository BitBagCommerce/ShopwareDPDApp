<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Controller;

use BitBag\ShopwareAppSystemBundle\Entity\ShopInterface;
use BitBag\ShopwareAppSystemBundle\Exception\ShopNotFoundException;
use BitBag\ShopwareAppSystemBundle\Repository\ShopRepositoryInterface;
use BitBag\ShopwareDpdApp\Entity\Config;
use BitBag\ShopwareDpdApp\Form\Type\ConfigType;
use BitBag\ShopwareDpdApp\Repository\ConfigRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ConfigurationModuleController extends AbstractController
{
    private ConfigRepositoryInterface $configRepository;

    private EntityManagerInterface $entityManager;

    private ShopRepositoryInterface $shopRepository;

    private TranslatorInterface $translator;

    public function __construct(
        ConfigRepositoryInterface $configRepository,
        EntityManagerInterface $entityManager,
        ShopRepositoryInterface $shopRepository,
        TranslatorInterface $translator
    ) {
        $this->configRepository = $configRepository;
        $this->entityManager = $entityManager;
        $this->shopRepository = $shopRepository;
        $this->translator = $translator;
    }

    public function __invoke(Request $request): Response
    {
        $session = $request->getSession();

        $shopId = $request->query->get('shop-id', '');

        /** @var ShopInterface|null $shop */
        $shop = $this->shopRepository->find($shopId);

        if (!$shop) {
            throw new ShopNotFoundException($shopId);
        }

        $config = $this->configRepository->findByShopId($shopId);

        if (!$config) {
            $config = new Config();
        }

        $form = $this->createForm(ConfigType::class, $config);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $config->setShop($shop);
            $this->entityManager->persist($config);
            $this->entityManager->flush();

            $session->getFlashBag()->add('success', $this->translator->trans('bitbag.shopware_dpd_app.config.saved'));
        }

        return $this->renderForm('configuration_module/index.html.twig', [
            'form' => $form,
        ]);
    }
}
