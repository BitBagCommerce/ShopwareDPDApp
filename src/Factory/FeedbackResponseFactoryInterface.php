<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use Symfony\Component\HttpFoundation\JsonResponse;

interface FeedbackResponseFactoryInterface
{
    public function returnError(string $messageKey): JsonResponse;

    public function returnSuccess(string $messageKey): JsonResponse;

    public function returnWarning(string $messageKey): JsonResponse;
}
