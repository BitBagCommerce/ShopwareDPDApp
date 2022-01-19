<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\AppSystem\Client;

interface ClientInterface
{
    public function fetchDetail(string $entityType, string $id): array;

    public function search(string $entityType, array $criteria): array;

    public function searchIds(string $entityType, array $criteria): array;

    public function createEntity(string $entityType, array $entityData): void;

    public function updateEntity(string $entityType, string $id, array $entityData): void;

    public function deleteEntity(string $entityType, string $id): void;
}
