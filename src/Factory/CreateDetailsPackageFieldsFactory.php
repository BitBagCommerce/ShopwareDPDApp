<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use BitBag\ShopwareDpdApp\AppSystem\Client\ClientInterface;
use BitBag\ShopwareDpdApp\Provider\CustomFieldNamesProviderInterface;
use BitBag\ShopwareDpdApp\Service\ClientApiService;

final class CreateDetailsPackageFieldsFactory implements CreateDetailsPackageFieldsFactoryInterface
{
    private CustomFieldNamesProviderInterface $customFieldNamesProvider;

    private ClientApiService $clientApiService;

    private CreateCustomFieldSetFactoryInterface $createCustomFieldSetFactory;

    private CreateCustomFieldFactoryInterface $createCustomFieldFactory;

    public function __construct(
        CustomFieldNamesProviderInterface $customFieldNamesProvider,
        ClientApiService $clientApiService,
        CreateCustomFieldSetFactoryInterface $createCustomFieldSetFactory,
        CreateCustomFieldFactoryInterface $createCustomFieldFactory
    ) {
        $this->customFieldNamesProvider = $customFieldNamesProvider;
        $this->clientApiService = $clientApiService;
        $this->createCustomFieldSetFactory = $createCustomFieldSetFactory;
        $this->createCustomFieldFactory = $createCustomFieldFactory;
    }

    public function create(ClientInterface $client): void
    {
        $customFieldPrefix = 'package_details';

        $customFieldNames = $this->customFieldNamesProvider->getFields();

        foreach ($customFieldNames as $key => $item) {
            $customFieldSetId = null;
            $type = $item['type'];

            $customFieldName = $customFieldPrefix.'_'.$item['name'];

            $customField = $this->clientApiService->findIdsCustomFieldByName($customFieldName, $client);
            if (0 !== $customField['total']) {
                return;
            }

            $customFieldSet = $this->clientApiService->findCustomFieldSetByName($customFieldPrefix, $client);
            if (0 === $customFieldSet['total']) {
                $customFieldSet = $this->createCustomFieldSetFactory->create(
                    $customFieldPrefix,
                    'Package details',
                    'order'
                );
            } else {
                $customFieldSetId = $customFieldSet['data'][0]['id'];
            }

            $this->createCustomFieldFactory->create(
                $customFieldName,
                $type,
                $key,
                $item['label'],
                $client,
                $customFieldSetId,
                $customFieldSet
            );
        }
    }
}
