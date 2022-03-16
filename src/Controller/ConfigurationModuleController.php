<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Controller;

use BitBag\ShopwareDpdApp\Entity\Config;
use BitBag\ShopwareDpdApp\Entity\ShopInterface;
use BitBag\ShopwareDpdApp\Form\Type\ConfigType;
use BitBag\ShopwareDpdApp\Repository\ConfigRepositoryInterface;
use BitBag\ShopwareDpdApp\Repository\ShopRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ConfigurationModuleController
{
    private Environment $template;

    private FormFactory $form;

    private ConfigRepositoryInterface $configRepository;

    private EntityManagerInterface $entityManager;

    private ShopRepositoryInterface $shopRepository;

    public function __construct(
        Environment $template,
        FormFactory $form,
        ConfigRepositoryInterface $configRepository,
        EntityManagerInterface $entityManager,
        ShopRepositoryInterface $shopRepository
    ) {
        $this->template = $template;
        $this->form = $form;
        $this->configRepository = $configRepository;
        $this->entityManager = $entityManager;
        $this->shopRepository = $shopRepository;
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function __invoke(Request $request): Response
    {
        $em = $this->entityManager;

        /** @var ShopInterface $shop */
        $shop = $this->shopRepository->find($request->get('shop-id'));

        $config = $this->configRepository->findOneBy(['shop' => $shop]);
        if (!$config) {
            $config = new Config();
        }

        $form = $this->form->create(ConfigType::class, $config);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $config->setShop($shop);
            $em->persist($config);
            $em->flush();
        }

        return new Response($this->template->render('configuration_module.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
