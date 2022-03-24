<?php

declare(strict_types=1);

namespace BitBag\ShopwareAppSkeleton\AppSystem\Client;

use BitBag\ShopwareAppSkeleton\AppSystem\Exception\ApiException;
use GuzzleHttp\Client as HttpClient;

final class Client implements ClientInterface
{
    private HttpClient $client;

    private string $shopUrl;

    public function __construct(HttpClient $client, string $shopUrl)
    {
        $this->client = $client;
        $this->shopUrl = $shopUrl;
    }

    public function fetchDetail(string $entityType, string $id): array
    {
        $requestPath = sprintf('/api/%s/%s', $entityType, $id);

        $response = $this->client->get($requestPath);

        if (200 !== $response->getStatusCode()) {
            throw new ApiException($this->shopUrl, $requestPath, $response);
        }

        /** @var array $result */
        $result = \json_decode($response->getBody()->getContents(), true);

        return $result;
    }

    public function search(string $entityType, array $criteria): array
    {
        $requestPath = sprintf('/api/search/%s', $entityType);

        $response = $this->client->post($requestPath, ['body' => \json_encode($criteria)]);

        if (200 !== $response->getStatusCode()) {
            throw new ApiException($this->shopUrl, $requestPath, $response);
        }

        /** @var array $result */
        $result = \json_decode($response->getBody()->getContents(), true);

        return $result;
    }

    public function searchIds(string $entityType, array $criteria): array
    {
        $requestPath = sprintf('/api/search-ids/%s', $entityType);

        $response = $this->client->post($requestPath, ['body' => \json_encode($criteria)]);

        if (200 !== $response->getStatusCode()) {
            throw new ApiException($this->shopUrl, $requestPath, $response);
        }

        /** @var array $result */
        $result = \json_decode($response->getBody()->getContents(), true);

        return $result;
    }

    public function createEntity(string $entityType, array $entityData): void
    {
        $requestPath = sprintf('/api/%s', $entityType);

        $response = $this->client->post($requestPath, ['body' => \json_encode($entityData)]);

        if (204 !== $response->getStatusCode()) {
            throw new ApiException($this->shopUrl, $requestPath, $response);
        }
    }

    public function updateEntity(string $entityType, string $id, array $entityData): void
    {
        $requestPath = sprintf('/api/%s/%s', $entityType, $id);

        $response = $this->client->patch($requestPath, ['body' => \json_encode($entityData)]);

        if (204 !== $response->getStatusCode()) {
            throw new ApiException($this->shopUrl, $requestPath, $response);
        }
    }

    public function deleteEntity(string $entityType, string $id): void
    {
        $requestPath = sprintf('/api/%s/%s', $entityType, $id);

        $response = $this->client->delete($requestPath);

        if (204 !== $response->getStatusCode()) {
            throw new ApiException($this->shopUrl, $requestPath, $response);
        }
    }
}
