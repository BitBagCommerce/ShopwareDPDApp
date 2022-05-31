<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Api;

use T3ko\Dpd\Request\GeneratePackageNumbersRequest;
use T3ko\Dpd\Soap\Types\GeneratePackagesNumbersV4Response;

interface ApiServiceInterface
{
    public const STATUS_OK = 'OK';

    public const INCORRECT_DATA = 'INCORRECT_DATA';

    public const INCORRECT_RECEIVER_POSTAL_CODE = 'INCORRECT_RECEIVER_POSTAL_CODE';

    public const INCORRECT_LOGIN_OR_PASSWORD = 'INCORRECT_LOGIN_OR_PASSWORD';

    public const DISALLOWED_FID = 'DISALLOWED_FID';

    public const ACCOUNT_IS_LOCKED = 'ACCOUNT_IS_LOCKED';

    public function setAuthData(string $login, string $password, int $masterFid, bool $isSandbox): void;

    public function createPackages(GeneratePackageNumbersRequest $request): GeneratePackagesNumbersV4Response;
}
