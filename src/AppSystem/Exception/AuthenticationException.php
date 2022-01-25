<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\AppSystem\Exception;

class AuthenticationException extends \Exception
{
    public function __construct(string $shopUrl, string $apiKey, string $reason)
    {
        $message = \sprintf(
            'Could not authenticate with store. Shop URL: %s, API key: %s, reason: %s',
            $shopUrl,
            $apiKey,
            $reason
        );

        parent::__construct($message, 0, null);
    }
}
