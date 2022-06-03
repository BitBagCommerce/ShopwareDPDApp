<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use BitBag\ShopwareAppSystemBundle\Model\Feedback\Notification\Error;
use BitBag\ShopwareAppSystemBundle\Model\Feedback\Notification\Success;
use BitBag\ShopwareAppSystemBundle\Model\Feedback\Notification\Warning;
use BitBag\ShopwareAppSystemBundle\Response\FeedbackResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

final class FeedbackResponseFactory implements FeedbackResponseFactoryInterface
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function createError(string $messageKey): JsonResponse
    {
        return new FeedbackResponse(new Error($this->translator->trans($messageKey)));
    }

    public function createSuccess(string $messageKey): JsonResponse
    {
        return new FeedbackResponse(new Success($this->translator->trans($messageKey, [])));
    }

    public function createWarning(string $messageKey): JsonResponse
    {
        return new FeedbackResponse(new Warning($this->translator->trans($messageKey)));
    }
}
