<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Api;

use BitBag\ShopwareDpdApp\Exception\ApiException;
use BitBag\ShopwareDpdApp\Exception\PackageException;
use BitBag\ShopwareDpdApp\Factory\ApiClassMapCollectionFactoryInterface;
use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapEngineFactory;
use Phpro\SoapClient\Soap\Driver\ExtSoap\ExtSoapOptions;
use Phpro\SoapClient\Soap\Handler\HttPlugHandle;
use stdClass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use T3ko\Dpd\Api;
use T3ko\Dpd\Request\GeneratePackageNumbersRequest;
use T3ko\Dpd\Soap\Client\AppServicesClient;
use T3ko\Dpd\Soap\Client\InfoServicesClient;
use T3ko\Dpd\Soap\Client\PackageServicesClient;
use T3ko\Dpd\Soap\Types\AuthDataV1;
use T3ko\Dpd\Soap\Types\GeneratePackagesNumbersV4Response;
use T3ko\Dpd\Soap\Types\PackagePGRV2;

/** @psalm-suppress PropertyNotSetInConstructor */
final class ApiService implements ApiServiceInterface
{
    public const WSDL_CACHE_NONE = 0;

    private ?string $login;

    private ?string $password;

    private ?int $masterFid;

    private ?bool $isSandbox;

    private ApiClassMapCollectionFactoryInterface $apiClassMapCollectionFactory;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        ApiClassMapCollectionFactoryInterface $apiClassMapCollectionFactory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->apiClassMapCollectionFactory = $apiClassMapCollectionFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function createPackages(GeneratePackageNumbersRequest $request): GeneratePackagesNumbersV4Response
    {
        $payload = $request->toPayload();
        $payload->setAuthData($this->getAuthDataStruct());

        try {
            $response = $this->createPackageServicesClient()->generatePackagesNumbersV4($payload);

            $responseStatus = $response->getReturn()->getStatus();

            if (self::STATUS_OK !== $responseStatus && self::DISALLOWED_FID === $responseStatus) {
                throw new ApiException('bitbag.shopware_dpd_app.api.provided_data_not_valid');
            }

            /** @var stdClass $responsePackages */
            $responsePackages = $response->getReturn()->getPackages();

            /** @var PackagePGRV2[] $packages */
            $packages = $responsePackages->Package;

            $firstPackage = $packages[0];

            $validationInfo = $firstPackage->getValidationDetails()->ValidationInfo[0] ?? null;

            if (($validationInfo && (ApiServiceInterface::INCORRECT_RECEIVER_POSTAL_CODE === $validationInfo->ErrorCode)) &&
                ApiServiceInterface::INCORRECT_DATA === $firstPackage->getStatus()
            ) {
                throw new PackageException('bitbag.shopware_dpd_app.order.address.post_code_invalid');
            }

            return $response;
        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), self::INCORRECT_LOGIN_OR_PASSWORD) ||
                false !== strpos(self::ACCOUNT_IS_LOCKED, $e->getMessage())
            ) {
                throw new ApiException('bitbag.shopware_dpd_app.api.provided_data_not_valid');
            }

            throw $e;
        }
    }

    public function setAuthData(string $login, string $password, int $masterFid, bool $isSandbox): void
    {
        $this->login = $login;
        $this->password = $password;
        $this->masterFid = $masterFid;
        $this->isSandbox = $isSandbox;
    }

    private function getAuthDataStruct(): AuthDataV1
    {
        $login = $this->login;
        $password = $this->password;
        $masterFid = $this->masterFid;

        if (null === $login || null === $password || null === $masterFid) {
            throw new ApiException('bitbag.shopware_dpd_app.config.not_found');
        }

        $authData = new AuthDataV1();
        $authData->setLogin($login);
        $authData->setPassword($password);
        $authData->setMasterFid($masterFid);

        return $authData;
    }

    private function createPackageServicesClient(): PackageServicesClient
    {
        $engine = ExtSoapEngineFactory::fromOptionsWithHandler(
            ExtSoapOptions::defaults(
                $this->getWsdl(PackageServicesClient::class),
                ['cache_wsdl' => self::WSDL_CACHE_NONE]
            )
                          ->withClassMap($this->apiClassMapCollectionFactory->create()),
            HttPlugHandle::createWithDefaultClient()
        );

        return new PackageServicesClient($engine, $this->eventDispatcher);
    }

    private function getWsdl(string $clientClass): string
    {
        if ($this->isSandbox) {
            return match ($clientClass) {
                PackageServicesClient::class => Api::PACKAGESERVICE_SANDBOX_WSDL_URL,
                AppServicesClient::class => Api::APPSERVICE_SANDBOX_WSDL_URL,
                InfoServicesClient::class => Api::INFOSERVICE_PRODUCTION_WSDL_URL,
                default => throw new ApiException('bitbag.shopware_dpd_app.api.wsdl_not_found'),
            };
        }

        return match ($clientClass) {
            PackageServicesClient::class => Api::PACKAGESERVICE_PRODUCTION_WSDL_URL,
            AppServicesClient::class => Api::APPSERVICE_PRODUCTION_WSDL_URL,
            InfoServicesClient::class => Api::INFOSERVICE_PRODUCTION_WSDL_URL,
            default => throw new ApiException('bitbag.shopware_dpd_app.api.wsdl_not_found'),
        };
    }
}
