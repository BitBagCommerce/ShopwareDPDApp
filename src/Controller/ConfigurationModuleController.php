<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Controller;

use BitBag\ShopwareAppSystemBundle\Exception\ShopNotFoundException;
use BitBag\ShopwareAppSystemBundle\Factory\Context\ContextFactoryInterface;
use BitBag\ShopwareAppSystemBundle\Repository\ShopRepositoryInterface;
use BitBag\ShopwareDpdApp\Entity\Config;
use BitBag\ShopwareDpdApp\Exception\ErrorNotificationException;
use BitBag\ShopwareDpdApp\Finder\SalesChannelFinderInterface;
use BitBag\ShopwareDpdApp\Form\Type\ConfigType;
use BitBag\ShopwareDpdApp\Provider\SalesChannelProviderInterface;
use BitBag\ShopwareDpdApp\Repository\ConfigRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ConfigurationModuleController extends AbstractController
{
    private ConfigRepositoryInterface $configRepository;

    private EntityManagerInterface $entityManager;

    private ShopRepositoryInterface $shopRepository;

    private TranslatorInterface $translator;

    private SalesChannelFinderInterface $salesChannelFinder;

    private ContextFactoryInterface $contextFactory;

    private SalesChannelProviderInterface $salesChannelProvider;

    public function __construct(
        ConfigRepositoryInterface $configRepository,
        EntityManagerInterface $entityManager,
        ShopRepositoryInterface $shopRepository,
        TranslatorInterface $translator,
        SalesChannelFinderInterface $salesChannelFinder,
        ContextFactoryInterface $contextFactory,
        SalesChannelProviderInterface $salesChannelProvider
    ) {
        $this->configRepository = $configRepository;
        $this->entityManager = $entityManager;
        $this->shopRepository = $shopRepository;
        $this->translator = $translator;
        $this->salesChannelFinder = $salesChannelFinder;
        $this->contextFactory = $contextFactory;
        $this->salesChannelProvider = $salesChannelProvider;
    }

    public function index(Request $request): Response
    {
        $session = $request->getSession();

        $shopId = $request->query->get('shop-id', '');

        $shop = $this->shopRepository->find($shopId);

        if (null === $shop) {
            throw new ShopNotFoundException($shopId);
        }

        $context = $this->contextFactory->create($shop);

        if (null === $context) {
            throw new UnauthorizedHttpException('');
        }

        $salesChannelsElements = $this->salesChannelFinder->findAll($context)->getEntities()->getElements();

        $salesChannels = array_merge(
            [null => 'All sales channel'],
            $this->salesChannelProvider->getForForm($salesChannelsElements),
        );

        try {
            $config = $this->configRepository->getByShopIdAndSalesChannelId($shopId);
        } catch (ErrorNotificationException) {
            $config = new Config();
        }

        $form = $this->createForm(ConfigType::class, $config, [
            'salesChannels' => $salesChannels,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $salesChannelId = $form->get('salesChannelId')->getData();

            try {
                $config = $this->configRepository->getByShopIdAndSalesChannelId($shopId, $salesChannelId);
            } catch (ErrorNotificationException) {
                /** @var Config $formData */
                $formData = $form->getData();

                $config = '' === $salesChannelId ? $formData : clone $formData;
            }

            $config->setShop($shop);
            $this->entityManager->persist($config);
            $this->entityManager->flush();

            $session->getFlashBag()->add('success', $this->translator->trans('bitbag.shopware_dpd_app.config.saved'));
        }

        return $this->renderForm('configuration_module/index.html.twig', [
            'form' => $form,
        ]);
    }

    public function getApiDataBySalesChannel(Request $request): JsonResponse
    {
        $shopId = $request->query->get('shop-id', '');
        $salesChannel = $request->query->get('salesChannel', '');

        try {
            $config = $this->configRepository->getByShopIdAndSalesChannelId($shopId, $salesChannel);
        } catch (ErrorNotificationException) {
            return new JsonResponse([
                'apiLogin' => null,
                'apiPassword' => null,
                'apiFid' => null,
                'apiEnvironment' => null,
                'senderFirstLastName' => null,
                'senderStreet' => null,
                'senderCity' => null,
                'senderZipCode' => null,
                'senderPhoneNumber' => null,
                'senderLocale' => null,
            ]);
        }

        $shop = $this->shopRepository->find($shopId);

        if (null === $shop) {
            throw new ShopNotFoundException($shopId);
        }

        $context = $this->contextFactory->create($shop);

        if (null === $context) {
            throw new UnauthorizedHttpException('');
        }

        return new JsonResponse([
            'apiLogin' => $config->getApiLogin(),
            'apiPassword' => $config->getApiPassword(),
            'apiFid' => $config->getApiFid(),
            'apiEnvironment' => $config->getApiEnvironment(),
            'senderFirstLastName' => $config->getSenderFirstLastName(),
            'senderStreet' => $config->getSenderStreet(),
            'senderCity' => $config->getSenderCity(),
            'senderZipCode' => $config->getSenderZipCode(),
            'senderPhoneNumber' => $config->getSenderPhoneNumber(),
            'senderLocale' => $config->getSenderLocale(),
        ]);
    }
}
