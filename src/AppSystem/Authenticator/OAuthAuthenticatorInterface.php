<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\AppSystem\Authenticator;

use BitBag\ShopwareAppSkeleton\AppSystem\Credentials\OAuthCredentialsInterface;
use BitBag\ShopwareAppSkeleton\Entity\ShopInterface;

interface OAuthAuthenticatorInterface
{
    public function authenticate(ShopInterface $shop): OAuthCredentialsInterface;
}
