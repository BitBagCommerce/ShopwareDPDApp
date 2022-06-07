<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Controller;

use BitBag\ShopwareDpdApp\Entity\ConfigInterface;
use BitBag\ShopwareDpdApp\Provider\Defaults;
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
        $shopId = $request->query->get('shopId');
        /** @var array{apiLogin: string, apiPassword: string, apiFid: string, apiEnvironment: string, senderFirstLastName: string, senderPhoneNumber: string, senderStreet: string, senderCity: string, senderZipCode: string, senderLocale: string} $formData */
        $formData = $request->query->get('formData');

        if (null === $shopId) {
            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
        }

        $api = new Api($formData['apiLogin'], $formData['apiPassword'], (int) $formData['apiFid']);
        $api->setSandboxMode($formData['apiEnvironment'] === ConfigInterface::SANDBOX_ENVIRONMENT);

        try {
            $api->generateLabels(GenerateLabelsRequest::fromWaybills(['00000000000000']));
        } catch (\Throwable $e) {
            if (Defaults::STATUS_DISALLOWED_FID === $e->getMessage() ||
                false !== strpos($e->getMessage(), Defaults::STATUS_INCORRECT_LOGIN_OR_PASSWORD) ||
                false !== strpos($e->getMessage(), Defaults::STATUS_ACCOUNT_IS_LOCKED)
            ) {
                return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
            }

            if (false === strpos($e->getMessage(), 'Return value must be of type string, null returned')) {
                return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
            }

            return new JsonResponse([]);
        }

        return new JsonResponse([]);
    }
}
