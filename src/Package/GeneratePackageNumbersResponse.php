<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Package;

use BitBag\ShopwareDpdApp\Exception\ApiException;
use BitBag\ShopwareDpdApp\Exception\PackageException;
use stdClass;
use T3ko\Dpd\Objects\RegisteredPackage;
use T3ko\Dpd\Objects\RegisteredParcel;
use T3ko\Dpd\Soap\Types\GeneratePackagesNumbersV4Response;
use T3ko\Dpd\Soap\Types\PackagePGRV2;
use T3ko\Dpd\Soap\Types\ParcelPGRV2;

final class GeneratePackageNumbersResponse
{
    public const STATUS_OK = 'OK';

    private $packages;

    /**
     * @param RegisteredPackage[] $packages
     */
    public function __construct(array $packages)
    {
        $this->packages = $packages;
    }

    public static function from(GeneratePackagesNumbersV4Response $response): self
    {
        $responseReturn = $response->getReturn();

        /** @var stdClass $packagesStdClass */
        $packagesStdClass = $responseReturn->getPackages();

        $packages = $packagesStdClass->Package;

        if (self::STATUS_OK !== $responseReturn->getStatus()) {
            if (false === empty($packages)) {
                $firstPackage = $packages[0];

                $validationInfo = $firstPackage->getValidationDetails()->ValidationInfo[0];

                if (('INCORRECT_DATA' === $firstPackage->getStatus()) &&
                    'INCORRECT_RECEIVER_POSTAL_CODE' === $validationInfo->ErrorCode
                ) {
                    throw new PackageException('bitbag.shopware_dpd_app.order.address.post_code_invalid');
                }

                throw new PackageException($validationInfo->ErrorCode);
            }

            if ('DISALLOWED_FID' === $responseReturn->getStatus()) {
                throw new ApiException('bitbag.shopware_dpd_app.api.provided_data_not_valid');
            }

            throw new PackageException($responseReturn->getStatus());
        }

        if (is_array($packages)) {
            $registeredPackages = [];

            /** @var PackagePGRV2 $package */
            foreach ($packages as $package) {
                $packageValidationDetails = [];
                if (null !== $package->getValidationDetails() && is_array($package->getValidationDetails()->ValidationInfo)) {
                    $packageValidationDetails = $package->getValidationDetails()->ValidationInfo;
                }

                $parcels = [];
                if (null !== $package->getParcels() && is_array($package->getParcels()->Parcel)) {
                    $parcels = $package->getParcels()->Parcel;
                }

                $registeredParcels = [];
                /** @var ParcelPGRV2 $parcel */
                foreach ($parcels as $parcel) {
                    $parcelValidationDetails = [];
                    if (null !== $parcel->getValidationDetails() && is_array($parcel->getValidationDetails()->ValidationInfo)) {
                        $parcelValidationDetails = $parcel->getValidationDetails()->ValidationInfo;
                    }

                    $registeredParcels[] = new RegisteredParcel(
                        $parcel->getParcelId(),
                        $parcel->getStatus(),
                        $parcel->getReference() ?? '',
                        $parcelValidationDetails,
                        $parcel->getWaybill()
                    );
                }

                $registeredPackages[] = new RegisteredPackage(
                    $package->getPackageId(),
                    $package->getStatus(),
                    $package->getReference(),
                    $packageValidationDetails,
                    $registeredParcels
                );
            }

            return new static($registeredPackages);
        }

        return new static([]);
    }

    /**
     * @return RegisteredPackage[]
     */
    public function getPackages(): array
    {
        return $this->packages;
    }
}
