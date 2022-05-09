<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\AppSystem\Authenticator;

use BitBag\ShopwareDpdApp\AppSystem\Credentials\OAuthCredentialsInterface;
use BitBag\ShopwareDpdApp\Entity\ShopInterface;

interface OAuthAuthenticatorInterface
{
    public function authenticate(ShopInterface $shop): OAuthCredentialsInterface;
}
