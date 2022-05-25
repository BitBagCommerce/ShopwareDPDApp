<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Provider;

use BitBag\ShopwareAppSystemBundle\Model\Feedback\Notification\Error;
use BitBag\ShopwareAppSystemBundle\Model\Feedback\Notification\Success;
use BitBag\ShopwareAppSystemBundle\Response\FeedbackResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\Translator;

final class NotificationProvider implements NotificationProviderInterface
{
    private Translator $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function returnNotificationError(string $messageKey, string $language = 'pl'): JsonResponse
    {
        return new FeedbackResponse(new Error($this->translator->trans($messageKey, [], null, $language)));
    }

    public function returnNotificationSuccess(string $messageKey, string $language = 'pl'): JsonResponse
    {
        return new FeedbackResponse(new Success($this->translator->trans($messageKey, [], null, $language)));
    }
}
