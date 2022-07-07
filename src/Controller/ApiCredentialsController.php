<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Controller;

use BitBag\ShopwareDpdApp\Entity\ConfigInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use T3ko\Dpd\Api;
use T3ko\Dpd\Request\GenerateLabelsRequest;

final class ApiCredentialsController
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function checkCredentials(Request $request): JsonResponse
    {
        $data = $request->toArray();
        $shopId = $data['shopId'];
        /** @var array{apiLogin: string, apiPassword: string, apiFid: string, apiEnvironment: string, senderFirstLastName: string, senderPhoneNumber: string, senderStreet: string, senderCity: string, senderZipCode: string} $formData */
        $formData = $data['formData'];
        $language = $data['language'];

        if (null === $shopId) {
            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
        }

        $api = new Api($formData['apiLogin'], $formData['apiPassword'], (int) $formData['apiFid']);
        $api->setSandboxMode(ConfigInterface::SANDBOX_ENVIRONMENT === $formData['apiEnvironment']);

        $isValid = $api->checkCredentialsByGenerateLabels(GenerateLabelsRequest::fromWaybills(['00000000000000']));

        $label = $isValid ?
            'bitbag.shopware_dpd_app.config.notification_label_success' :
            'bitbag.shopware_dpd_app.config.notification_label_error';
        $message = $isValid ?
            'bitbag.shopware_dpd_app.config.notification_message_success' :
            'bitbag.shopware_dpd_app.config.notification_message_error';

        return new JsonResponse([
            'label' => $this->translator->trans($label, [], null, $language),
            'message' => $this->translator->trans($message, [], null, $language),
        ], $isValid ? Response::HTTP_OK : Response::HTTP_UNAUTHORIZED);
    }
}
