<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Validator;

use BitBag\ShopwareDpdApp\AppSystem\Client\ClientInterface;
use BitBag\ShopwareDpdApp\Model\OrderModel;

interface ValidatorRequestDataInterface
{
    public function validate(ClientInterface $client, OrderModel $orderModel): void;
}
