<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Provider;

final class SalesChannelProvider implements SalesChannelProviderInterface
{
    public function getForForm(array $salesChannels): array
    {
        $items = [];

        foreach ($salesChannels as $salesChannel) {
            $items[$salesChannel->id] = $salesChannel->name;
        }

        return $items;
    }
}
