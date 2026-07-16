<?php
/*
 *  Copyright (c) 2026 Borlabs GmbH. All rights reserved.
 *  This file may not be redistributed in whole or significant part.
 *  Content of this file is protected by international copyright laws.
 *
 *  ----------------- Borlabs Cookie IS NOT FREE SOFTWARE -----------------
 *
 *  @copyright Borlabs GmbH, https://borlabs.io
 */

declare(strict_types=1);

namespace Borlabs\Cookie\System\GeoIp;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\ApiClient\GeoIpApiClient;
use Borlabs\Cookie\Dependencies\GeoIp2\Database\Reader;
use Borlabs\Cookie\Dependencies\GeoIp2\Exception\AddressNotFoundException;
use Borlabs\Cookie\Dependencies\MaxMind\Db\Reader\InvalidDatabaseException;
use Borlabs\Cookie\Dto\System\ExternalFileDto;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\Exception\RestEndpoint\InvalidRemoteIpRestInputException;
use Borlabs\Cookie\Exception\System\GeoIpException;
use Borlabs\Cookie\Exception\System\LicenseExpiredException;
use Borlabs\Cookie\Localization\GeoIp\CountryLocalizationStrings;
use Borlabs\Cookie\Model\Country\CountryModel;
use Borlabs\Cookie\Repository\Country\CountryRepository;
use Borlabs\Cookie\System\Config\DialogSettingsConfig;
use Borlabs\Cookie\System\FileSystem\FileManager;
use Borlabs\Cookie\System\License\License;
use Borlabs\Cookie\System\Log\Log;
use Borlabs\Cookie\System\Option\Option;
use InvalidArgumentException;

class GeoIp
{
    public const OPTION_GEO_IP_DB_CURRENT_ID = 'GeoIpCurrentId';

    public const OPTION_GEO_IP_DB_FILE_NAME = 'GeoIpFileName';

    public const OPTION_GEO_IP_DB_LAST_API_CHECK = 'GeoIpLastDatabaseUpdate';

    public const OPTION_GEO_IP_DB_LAST_UPDATE = 'GeoIpLastSuccessfulCheckWithApi';

    private CountryRepository $countryRepository;

    private DialogSettingsConfig $dialogSettingsConfig;

    private FileManager $fileManager;

    private GeoIpApiClient $geoIpApiClient;

    private License $license;

    private Log $log;

    private Option $option;

    private WpFunction $wpFunction;

    public function __construct(
        CountryRepository $countryRepository,
        DialogSettingsConfig $dialogSettingsConfig,
        FileManager $fileManager,
        GeoIpApiClient $geoIpApiClient,
        License $license,
        Log $log,
        Option $option,
        WpFunction $wpFunction
    ) {
        $this->countryRepository = $countryRepository;
        $this->dialogSettingsConfig = $dialogSettingsConfig;
        $this->fileManager = $fileManager;
        $this->geoIpApiClient = $geoIpApiClient;
        $this->log = $log;
        $this->license = $license;
        $this->option = $option;
        $this->wpFunction = $wpFunction;
    }

    public function deleteDatabase(): void
    {
        $oldDatabase = $this->getOptionFileName();

        $this->option->deleteGlobal(self::OPTION_GEO_IP_DB_CURRENT_ID);
        $this->option->deleteGlobal(self::OPTION_GEO_IP_DB_FILE_NAME);
        $this->option->deleteGlobal(self::OPTION_GEO_IP_DB_LAST_API_CHECK);
        $this->option->deleteGlobal(self::OPTION_GEO_IP_DB_LAST_UPDATE);

        if ($oldDatabase !== null) {
            $this->fileManager->deleteGloballyCachedFile($oldDatabase);
            $this->fileManager->deleteGloballyStoredFile($oldDatabase);
        }
    }

    /**
     * @throws \Borlabs\Cookie\Exception\ApiClient\ApiClientInvalidLicenseException
     * @throws \Borlabs\Cookie\Exception\ApiClient\GeoIpApiClientException
     * @throws \Borlabs\Cookie\Exception\System\GeoIpException
     * @throws \Borlabs\Cookie\Exception\System\LicenseExpiredException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ConnectionErrorException
     */
    public function downloadGeoIpDatabase(bool $force = false): bool
    {
        if ($this->license->get() === null) {
            return false;
        }

        if (!$this->license->isLicenseValid()) {
            throw new LicenseExpiredException('licenseExpiredFeatureNotAvailable');
        }

        $geoIpAttachment = $this->geoIpApiClient->requestGeoIpDatabaseAttachmentData();
        $this->option->setGlobal(self::OPTION_GEO_IP_DB_LAST_API_CHECK, time());

        if ($force === false && $this->getOptionCurrentId() === $geoIpAttachment->id) {
            return true;
        }

        $geoIpZipFile = $this->fileManager->storeExternalFileGlobally(
            new ExternalFileDto($geoIpAttachment->downloadUrl),
            'geo-ip-database-' . $geoIpAttachment->id . '-' . bin2hex(random_bytes(5)) . '.mmdb.zip',
        );

        if ($geoIpZipFile === null) {
            throw new GeoIpException('downloadFailed');
        }

        $temporaryDirectory = $this->fileManager->createTemporaryGlobalStorageFolder();

        if ($temporaryDirectory === null) {
            throw new GeoIpException('createTemporaryFolderFailed');
        }

        $unzipStatus = $this->wpFunction->unzipFile(
            $geoIpZipFile->fullPath,
            $temporaryDirectory->fullPath,
        );

        if ($unzipStatus !== true) {
            throw new GeoIpException('unzipFailed');
        }

        $geoIpDatabseFileName = 'geo-ip-database-' . $geoIpAttachment->id . '-' . bin2hex(random_bytes(5)) . '.mmdb';
        $moveStatus = $this->fileManager->moveGloballyStoredFile(
            $temporaryDirectory->directoryName . '/geo-ip.mmdb',
            $geoIpDatabseFileName,
            true,
        );

        if ($moveStatus !== true) {
            throw new GeoIpException('fileMoveFailed');
        }

        $this->fileManager->deleteTemporaryGlobalStorageFolder($temporaryDirectory->directoryName);
        $this->fileManager->deleteGloballyStoredFile($geoIpZipFile->fileName);

        $oldDatabase = $this->getOptionFileName();

        $this->option->setGlobal(self::OPTION_GEO_IP_DB_CURRENT_ID, $geoIpAttachment->id);
        $this->option->setGlobal(self::OPTION_GEO_IP_DB_FILE_NAME, $geoIpDatabseFileName);
        $this->option->setGlobal(self::OPTION_GEO_IP_DB_LAST_UPDATE, (string) time());

        // Generate new cache
        $this->generateDatabaseCacheIfMissing();

        // Remove old database
        if ($oldDatabase !== null) {
            $this->fileManager->deleteGloballyStoredFile($oldDatabase);
        }

        return true;
    }

    public function generateDatabaseCacheIfMissing(): bool
    {
        if ($this->fileManager->isGloballyCachedFilePresent($this->getOptionFileName())) {
            return true;
        }

        return $this->fileManager->copyFile(
            $this->getOptionFileName(),
            $this->fileManager->getGlobalStorageFolder(),
            $this->fileManager->getGlobalCacheFolder(),
        );
    }

    public function getAllCountriesGroupedByUnionsLocalized(): KeyValueDtoList
    {
        $localized = CountryLocalizationStrings::get();
        $euCountries = new KeyValueDtoList();
        $nonEuCountries = new KeyValueDtoList();
        $countries = $this->countryRepository->find();

        foreach ($countries as $country) {
            if ($country->isEuropeanUnion) {
                $euCountries->add(new KeyValueDto($country->code, $localized['countries'][$country->code]));
            } else {
                $nonEuCountries->add(new KeyValueDto($country->code, $localized['countries'][$country->code]));
            }
        }

        $euCountries->sortListByPropertiesNaturally(['value']);
        $nonEuCountries->sortListByPropertiesNaturally(['value']);

        return new KeyValueDtoList([
            new KeyValueDto('eu', $euCountries),
            new KeyValueDto('nonEu', $nonEuCountries),
        ]);
    }

    public function getCountryForIpAddress(string $ipAddress): ?CountryModel
    {
        if (!$this->isGeoIpDatabaseDownloaded()) {
            return null;
        }

        $this->generateDatabaseCacheIfMissing();

        try {
            $reader = new Reader($this->getDatabaseCacheFullPath());
            $record = $reader->country($ipAddress);
        } catch (AddressNotFoundException $exception) {
            return null;
        } catch (InvalidDatabaseException $exception) {
            $this->log->error('Invalid GeoIp database', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return null;
        } catch (InvalidArgumentException $exception) {
            $this->log->error('Problem with reading GeoIp database from cache', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return null;
        }

        $countries = $this->countryRepository->find([
            'code' => $record->country->isoCode,
        ]);

        if (empty($countries)) {
            return null;
        }

        return $countries[0];
    }

    /**
     * @throws \Borlabs\Cookie\Exception\RestEndpoint\InvalidRemoteIpRestInputException
     */
    public function getCountryForUserIp(): ?CountryModel
    {
        $ipAddress = $this->getUserIp();

        return $this->getCountryForIpAddress($ipAddress);
    }

    public function getDatabaseCacheFullPath(): string
    {
        return $this->fileManager->getGlobalCacheFolder()->getPath() . '/' . $this->getOptionFileName();
    }

    public function getLastSuccessfulCheckWithApiTimestamp(): ?int
    {
        $option = $this->option->getGlobal(self::OPTION_GEO_IP_DB_LAST_API_CHECK);

        return $option->value !== false ? (int) ($option->value) : null;
    }

    public function getOptionCurrentId(): ?string
    {
        $option = $this->option->getGlobal(self::OPTION_GEO_IP_DB_CURRENT_ID);

        return $option->value !== false ? $option->value : null;
    }

    public function getOptionFileName(): ?string
    {
        $option = $this->option->getGlobal(self::OPTION_GEO_IP_DB_FILE_NAME);

        return $option->value !== false ? $option->value : null;
    }

    /**
     * @throws \Borlabs\Cookie\Exception\RestEndpoint\InvalidRemoteIpRestInputException
     */
    public function getShowDialogStatusForCurrentUser(?string $languageCode = null): bool
    {
        if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Borlabs-Scanner') !== false) {
            return true;
        }

        $dialogSettings = $this->dialogSettingsConfig->get();

        if ($languageCode) {
            $dialogSettings = $this->dialogSettingsConfig->load($languageCode);
        }

        if ($dialogSettings->geoIpActive === false) {
            return true;
        }

        $country = $this->getCountryForUserIp();

        if ($country !== null) {
            return !in_array($country->code, $dialogSettings->geoIpCountriesWithHiddenDialog, true);
        }

        return true;
    }

    public function isGeoIpDatabaseDownloaded(): bool
    {
        return $this->getOptionFileName() !== null
            && $this->getOptionCurrentId() !== null
            && $this->fileManager->isGloballyStoredFilePresent($this->getOptionFileName());
    }

    /**
     * @throws InvalidRemoteIpRestInputException
     */
    private function getUserIp(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP) !== false) {
            //check ip from share internet
            return $_SERVER['HTTP_CLIENT_IP'];
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP) !== false) {
            //to check ip is pass from proxy
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        if (!empty($_SERVER['REMOTE_ADDR']) && filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP) !== false) {
            return $_SERVER['REMOTE_ADDR'];
        }

        throw new InvalidRemoteIpRestInputException();
    }
}
