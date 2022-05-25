<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Provider;

use Symfony\Component\HttpFoundation\Response;

interface NotificationProviderInterface
{
    public function returnNotificationError(string $messageKey, string $language = 'pl'): Response;

    public function returnNotificationSuccess(string $messageKey, string $language = 'pl'): Response;
}
