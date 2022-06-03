<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use Symfony\Component\HttpFoundation\JsonResponse;

interface FeedbackResponseFactoryInterface
{
    public function createError(string $messageKey): JsonResponse;

    public function createSuccess(string $messageKey): JsonResponse;

    public function createWarning(string $messageKey): JsonResponse;
}
