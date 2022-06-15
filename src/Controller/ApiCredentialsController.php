<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Controller;

use BitBag\ShopwareDpdApp\Entity\ConfigInterface;
use BitBag\ShopwareDpdApp\Resolver\ApiClientResolverInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use T3ko\Dpd\Api;
use T3ko\Dpd\Request\GenerateLabelsRequest;

final class ApiCredentialsController
{
    private ApiClientResolverInterface $apiClientResolver;

    public function __construct(ApiClientResolverInterface $apiClientResolver)
    {
        $this->apiClientResolver = $apiClientResolver;
    }

    public function checkCredentials(Request $request): JsonResponse
    {
        $data = $request->toArray();
        $shopId = $data['shopId'];
        /** @var array{apiLogin: string, apiPassword: string, apiFid: string, apiEnvironment: string, senderFirstLastName: string, senderPhoneNumber: string, senderStreet: string, senderCity: string, senderZipCode: string, senderLocale: string} $formData */
        $formData = $data['formData'];

        if (null === $shopId) {
            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
        }

        $api = new Api($formData['apiLogin'], $formData['apiPassword'], (int) $formData['apiFid']);
        $api->setSandboxMode(ConfigInterface::SANDBOX_ENVIRONMENT === $formData['apiEnvironment']);

        $isValid = $api->checkCredentialsByGenerateLabels(GenerateLabelsRequest::fromWaybills(['00000000000000']));

        return $isValid ? new JsonResponse([]) : new JsonResponse([], Response::HTTP_UNAUTHORIZED);
    }
}
