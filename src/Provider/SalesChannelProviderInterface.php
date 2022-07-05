<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Provider;

interface SalesChannelProviderInterface
{
    public function getForForm(array $salesChannels): array;
}
