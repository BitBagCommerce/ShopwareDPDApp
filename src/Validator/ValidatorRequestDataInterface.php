<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\Validator;

use BitBag\ShopwareAppSkeleton\AppSystem\Client\ClientInterface;
use BitBag\ShopwareAppSkeleton\Model\Order;

interface ValidatorRequestDataInterface
{
    public function validate(ClientInterface $client, Order $orderModel): array;
}
