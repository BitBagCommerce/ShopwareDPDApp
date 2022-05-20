<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ConfigNotFoundException extends NotFoundHttpException
{
}
