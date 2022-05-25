<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use Symfony\Component\HttpFoundation\JsonResponse;

interface FeedbackResponseFactoryInterface
{
    public const DEFAULT_LANGUAGE = 'pl';

    public function returnError(string $messageKey, string $language = self::DEFAULT_LANGUAGE): JsonResponse;

    public function returnSuccess(string $messageKey, string $language = self::DEFAULT_LANGUAGE): JsonResponse;

    public function returnWarning(string $messageKey, string $language = self::DEFAULT_LANGUAGE): JsonResponse;
}
