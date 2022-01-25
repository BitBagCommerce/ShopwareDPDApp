<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\AppSystem\Exception;

use Psr\Http\Message\ResponseInterface;

class ApiException extends \Exception
{
    public function __construct(string $shopUrl, string $requestPath, ResponseInterface $response)
    {
        $message = \sprintf(
            'Error occurred while requesting %s from shop %s, got status %s and response was %s',
            $requestPath,
            $shopUrl,
            $response->getStatusCode(),
            $response->getBody()->getContents()
        );

        parent::__construct($message);
    }
}
