<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class ApiException extends BadRequestHttpException
{
    public function getErrorCode(): string
    {
        return 'BITBAG_DPD_APP__API_EXCEPTION';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
