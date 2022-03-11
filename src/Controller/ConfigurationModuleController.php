<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\Controller;

use BitBag\ShopwareAppSkeleton\Entity\Config;
use BitBag\ShopwareAppSkeleton\Form\Type\ConfigType;
use BitBag\ShopwareAppSkeleton\Repository\ConfigRepositoryInterface;
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

    public function __construct(
        Environment $template,
        FormFactory $form,
        ConfigRepositoryInterface $configRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->template = $template;
        $this->form = $form;
        $this->configRepository = $configRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function __invoke(Request $request): Response
    {
        $em = $this->entityManager;

        $config = $this->configRepository->findOneBy([]);
        if (!$config) {
            $config = new Config();
        }

        $form = $this->form->create(ConfigType::class, $config);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($config);
            $em->flush();
        }

        return new Response($this->template->render('configuration_module.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
