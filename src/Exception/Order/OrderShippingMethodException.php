<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Exception\Order;

use Symfony\Component\HttpFoundation\Response;

final class OrderShippingMethodException extends \LogicException
{
    public function getErrorCode(): string
    {
        return 'BITBAG_DPD_APP__ORDER_SHIPPING_METHOD_EXCEPTION';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
