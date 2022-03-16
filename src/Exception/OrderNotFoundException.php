<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderNotFoundException extends NotFoundHttpException
{
    public function __construct(?string $message = null)
    {
        parent::__construct($message ?? 'Order was not found');
    }
}
