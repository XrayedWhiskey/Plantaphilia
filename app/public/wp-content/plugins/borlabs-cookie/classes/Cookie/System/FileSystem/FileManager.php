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

namespace Borlabs\Cookie\System\FileSystem;

use Borlabs\Cookie\Adapter\WpFilesystem;
use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Dto\System\DirectoryDto;
use Borlabs\Cookie\Dto\System\ExternalFileDto;
use Borlabs\Cookie\Dto\System\FileDto;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\System\Log\Log;
use Exception;

final class FileManager
{
    private CacheFolder $cacheFolder;

    private GlobalCacheFolder $globalCacheFolder;

    private GlobalStorageFolder $globalStorageFolder;

    private Log $log;

    private StorageFolder $storageFolder;

    private WpFunction $wpFunction;

    /**
     * @param \Borlabs\Cookie\System\FileSystem\CacheFolder         $cacheFolder
     * @param \Borlabs\Cookie\System\FileSystem\GlobalCacheFolder   $globalCacheFolder
     * @param \Borlabs\Cookie\System\FileSystem\GlobalStorageFolder $globalStorageFolder
     * @param \Borlabs\Cookie\System\FileSystem\StorageFolder       $storageFolder
     */
    public function __construct(
        CacheFolder $cacheFolder,
        GlobalCacheFolder $globalCacheFolder,
        GlobalStorageFolder $globalStorageFolder,
        Log $log,
        StorageFolder $storageFolder,
        WpFunction $wpFunction
    ) {
        $this->cacheFolder = $cacheFolder;
        $this->globalCacheFolder = $globalCacheFolder;
        $this->globalStorageFolder = $globalStorageFolder;
        $this->log = $log;
        $this->storageFolder = $storageFolder;
        $this->wpFunction = $wpFunction;
    }

    /**
     * @param array $requestOptions optional array of options for the wp_remote_get request, such as timeout,
     *                              headers, etc
     */
    public function cacheExternalFile(ExternalFileDto $externalFileDto, ?string $fileName = null, array $requestOptions = []): ?FileDto
    {
        return $this->saveExternalFile($externalFileDto, $this->cacheFolder, $fileName, $requestOptions);
    }

    /**
     * @param array $requestOptions optional array of options for the wp_remote_get request, such as timeout,
     *                              headers, etc
     */
    public function cacheExternalFileGlobally(ExternalFileDto $externalFileDto, ?string $fileName = null, array $requestOptions = []): ?FileDto
    {
        return $this->saveExternalFile($externalFileDto, $this->globalCacheFolder, $fileName, $requestOptions);
    }

    /**
     * @throws \Borlabs\Cookie\Exception\GenericException
     */
    public function cacheFile(string $fileName, string $fileContent): ?FileDto
    {
        return $this->saveFile($fileName, $fileContent, $this->cacheFolder);
    }

    /**
     * @throws \Borlabs\Cookie\Exception\GenericException
     */
    public function cacheFileGlobally(string $fileName, string $fileContent): ?FileDto
    {
        return $this->saveFile($fileName, $fileContent, $this->globalCacheFolder);
    }

    /**
     * @param \Borlabs\Cookie\System\FileSystem\FileLocationInterface $sourceLocation
     * @param \Borlabs\Cookie\System\FileSystem\FileLocationInterface $destinationLocation
     */
    public function copyFile(
        string $fileName,
        FileLocationInterface $sourceLocation,
        FileLocationInterface $destinationLocation,
        bool $overwriteIfExists = false,
        ?int $mode = null
    ): bool {
        if ($this->exists($sourceLocation->getPath() . '/' . $fileName) === false) {
            return false;
        }

        return WpFilesystem::getInstance()->copy(
            $sourceLocation->getPath() . '/' . $fileName,
            $destinationLocation->getPath() . '/' . $fileName,
            $overwriteIfExists,
            $mode ?? false,
        );
    }

    public function createTemporaryGlobalStorageFolder(): ?DirectoryDto
    {
        return $this->createTemporaryFolder($this->globalStorageFolder);
    }

    public function createTemporaryStorageFolder(): ?DirectoryDto
    {
        return $this->createTemporaryFolder($this->storageFolder);
    }

    public function deleteCacheFolder(): bool
    {
        return $this->delete('', $this->cacheFolder, true);
    }

    public function deleteGlobalCacheFolder(): bool
    {
        return $this->delete('', $this->globalCacheFolder, true);
    }

    public function deleteGloballyCachedFile(string $fileName): bool
    {
        return $this->delete($fileName, $this->globalCacheFolder);
    }

    public function deleteGloballyStoredFile(string $fileName): bool
    {
        return $this->delete($fileName, $this->globalStorageFolder);
    }

    public function deleteGlobalStorageFolder(): bool
    {
        return $this->delete('', $this->globalStorageFolder, true);
    }

    public function deleteStorageFolder(): bool
    {
        return $this->delete('', $this->storageFolder, true);
    }

    public function deleteStoredFile(string $fileName): bool
    {
        return $this->delete($fileName, $this->storageFolder);
    }

    public function deleteTemporaryGlobalStorageFolder(string $directoryName): bool
    {
        return $this->delete($directoryName, $this->globalStorageFolder, true);
    }

    /**
     * @param mixed $withChecksum
     *
     * @throws \Borlabs\Cookie\Exception\GenericException
     */
    public function getCachedFile(string $fileName, $withChecksum = false): ?FileDto
    {
        return $this->getFile($fileName, $this->cacheFolder, $withChecksum);
    }

    public function getCachedFileContent(string $fileName): ?string
    {
        return $this->getFileContent($fileName, $this->cacheFolder);
    }

    /**
     * @return \Borlabs\Cookie\System\FileSystem\CacheFolder
     */
    public function getCacheFolder(): CacheFolder
    {
        return $this->cacheFolder;
    }

    /**
     * @return \Borlabs\Cookie\System\FileSystem\GlobalCacheFolder
     */
    public function getGlobalCacheFolder(): GlobalCacheFolder
    {
        return $this->globalCacheFolder;
    }

    /**
     * @param mixed $withChecksum
     *
     * @throws \Borlabs\Cookie\Exception\GenericException
     */
    public function getGloballyCachedFile(string $fileName, $withChecksum = false): ?FileDto
    {
        return $this->getFile($fileName, $this->globalCacheFolder, $withChecksum);
    }

    public function getGloballyCachedFileContent(string $fileName): ?string
    {
        return $this->getFileContent($fileName, $this->globalCacheFolder);
    }

    /**
     * @param mixed $withChecksum
     *
     * @throws \Borlabs\Cookie\Exception\GenericException
     */
    public function getGloballyStoredFile(string $fileName, $withChecksum = false): ?FileDto
    {
        return $this->getFile($fileName, $this->globalStorageFolder, $withChecksum);
    }

    public function getGloballyStoredFileContent(string $fileName): ?string
    {
        return $this->getFileContent($fileName, $this->globalStorageFolder);
    }

    /**
     * @return \Borlabs\Cookie\System\FileSystem\GlobalStorageFolder
     */
    public function getGlobalStorageFolder(): GlobalStorageFolder
    {
        return $this->globalStorageFolder;
    }

    /**
     * @return \Borlabs\Cookie\System\FileSystem\StorageFolder
     */
    public function getStorageFolder(): StorageFolder
    {
        return $this->storageFolder;
    }

    /**
     * @param mixed $withChecksum
     *
     * @throws \Borlabs\Cookie\Exception\GenericException
     */
    public function getStoredFile(string $fileName, $withChecksum = false): ?FileDto
    {
        return $this->getFile($fileName, $this->storageFolder, $withChecksum);
    }

    public function getStoredFileContent(string $fileName): ?string
    {
        return $this->getFileContent($fileName, $this->storageFolder);
    }

    public function isCachedFilePresent(string $fileName): bool
    {
        return $this->exists($this->cacheFolder->getPath() . '/' . $fileName);
    }

    public function isGloballyCachedFilePresent(string $fileName): bool
    {
        return $this->exists($this->globalCacheFolder->getPath() . '/' . $fileName);
    }

    public function isGloballyStoredFilePresent(string $fileName): bool
    {
        return $this->exists($this->globalStorageFolder->getPath() . '/' . $fileName);
    }

    public function isStoredFilePresent(string $fileName): bool
    {
        return $this->exists($this->storageFolder->getPath() . '/' . $fileName);
    }

    public function moveGloballyStoredFile(
        string $fromFileName,
        string $toFileName,
        bool $overwriteIfExists = false
    ): bool {
        return $this->moveFile($fromFileName, $toFileName, $this->globalStorageFolder, $overwriteIfExists);
    }

    /**
     * @param array $requestOptions optional array of options for the wp_remote_get request, such as timeout,
     *                              headers, etc
     */
    public function storeExternalFile(ExternalFileDto $externalFileDto, ?string $fileName = null, array $requestOptions = []): ?FileDto
    {
        return $this->saveExternalFile($externalFileDto, $this->storageFolder, $fileName, $requestOptions);
    }

    /**
     * @param array $requestOptions optional array of options for the wp_remote_get request, such as timeout,
     *                              headers, etc
     */
    public function storeExternalFileGlobally(ExternalFileDto $externalFileDto, ?string $fileName = null, array $requestOptions = []): ?FileDto
    {
        return $this->saveExternalFile($externalFileDto, $this->globalStorageFolder, $fileName, $requestOptions);
    }

    /**
     * @throws \Borlabs\Cookie\Exception\GenericException
     */
    public function storeFile(string $fileName, string $fileContent): ?FileDto
    {
        return $this->saveFile($fileName, $fileContent, $this->storageFolder);
    }

    /**
     * @throws \Borlabs\Cookie\Exception\GenericException
     */
    public function storeFileGlobally(string $fileName, string $fileContent): ?FileDto
    {
        return $this->saveFile($fileName, $fileContent, $this->globalStorageFolder);
    }

    /**
     * Creates a directory recursively.
     * It uses the permission of last existing directory from the given path.
     * This method is a wrapper for the WordPress function `wp_mkdir_p`.
     */
    private function createDirectoryRecursively(string $path): bool
    {
        return $this->wpFunction->wpMkdirP($path);
    }

    /**
     * @param \Borlabs\Cookie\System\FileSystem\FileLocationInterface $location
     *
     * @throws Exception
     */
    private function createTemporaryFolder(FileLocationInterface $location): ?DirectoryDto
    {
        $uniqueFolderNameFound = false;
        while ($uniqueFolderNameFound === false) {
            $uniqueFolderName = 'tmp-' . date('Ymd') . '-' . bin2hex(random_bytes(5));

            if ($this->exists($location->getPath() . '/' . $uniqueFolderName) === false) {
                $uniqueFolderNameFound = true;
            }
        }

        if ($this->mkdir($location->getPath() . '/' . $uniqueFolderName)) {
            return new DirectoryDto(
                $uniqueFolderName,
                $location->getPath(),
                $location->getUrl() . '/' . $uniqueFolderName,
            );
        }

        return null;
    }

    /**
     * @param \Borlabs\Cookie\System\FileSystem\FileLocationInterface $location
     */
    private function delete(string $relativeDirectoryOrFileName, FileLocationInterface $location, bool $recursive = false): bool
    {
        if ($this->exists($location->getPath() . '/' . $relativeDirectoryOrFileName) === false) {
            return false;
        }

        return WpFilesystem::getInstance()->delete($location->getPath() . '/' . $relativeDirectoryOrFileName, $recursive);
    }

    /**
     * @param \Borlabs\Cookie\System\FileSystem\FileLocationInterface $location
     */
    private function ensureFolderIsWritable(FileLocationInterface $location): bool
    {
        $path = $location->getPath();

        return $this->createDirectoryRecursively($path) && $this->isWritable($path);
    }

    private function exists(string $filePath): bool
    {
        return WpFilesystem::getInstance()->exists($filePath);
    }

    private function getChecksum(string $fileContent): string
    {
        return hash('sha256', $fileContent);
    }

    /**
     * @param \Borlabs\Cookie\System\FileSystem\FileLocationInterface $location
     * @param mixed                                                   $withChecksum
     *
     * @throws \Borlabs\Cookie\Exception\GenericException
     */
    private function getFile(string $fileName, FileLocationInterface $location, $withChecksum = false): ?FileDto
    {
        if (!$this->exists($location->getPath() . '/' . $fileName)) {
            $this->log->error(
                'Could not access file',
                [
                    'fileName' => $fileName,
                    'path' => $location->getPath(),
                ],
            );

            return null;
        }

        $checksum = null;

        if ($withChecksum) {
            $fileContent = $this->getFileContent($fileName, $location);

            if ($fileContent === null) {
                $this->log->error(
                    'File not readable',
                    [
                        'fileName' => $fileName,
                        'path' => $location->getPath(),
                    ],
                );

                throw new GenericException('fileNotReadable');
            }

            $checksum = $this->getChecksum($fileContent);
        }

        return new FileDto(
            $fileName,
            $location->getPath(),
            $checksum,
            $location->getUrl() . '/' . $fileName,
        );
    }

    /**
     * @param \Borlabs\Cookie\System\FileSystem\FileLocationInterface $location
     */
    private function getFileContent(string $fileName, FileLocationInterface $location): ?string
    {
        $fileContent = WpFilesystem::getInstance()->get_contents($location->getPath() . '/' . $fileName);

        if ($fileContent === false) {
            return null;
        }

        return $fileContent;
    }

    private function isWritable(string $path)
    {
        return WpFilesystem::getInstance()->is_writable($path);
    }

    private function mkdir(string $path): bool
    {
        return WpFilesystem::getInstance()->mkdir($path);
    }

    /**
     * @param \Borlabs\Cookie\System\FileSystem\FileLocationInterface $location
     */
    private function moveFile(
        string $fromFileName,
        string $toFileName,
        FileLocationInterface $location,
        bool $overwriteIfExists = false
    ): bool {
        return WpFilesystem::getInstance()->move(
            $location->getPath() . '/' . $fromFileName,
            $location->getPath() . '/' . $toFileName,
            $overwriteIfExists,
        );
    }

    /**
     * @param \Borlabs\Cookie\System\FileSystem\FileLocationInterface $location
     *
     * @throws \Borlabs\Cookie\Exception\GenericException
     */
    private function saveExternalFile(ExternalFileDto $externalFileDto, FileLocationInterface $location, ?string $fileName = null, array $requestOptions = []): ?FileDto
    {
        $responseDto = $this->wpFunction->wpRemoteGet($externalFileDto->url, $requestOptions);

        if ($responseDto->responseCode === null
            || $responseDto->responseCode < 200
            || $responseDto->responseCode > 299
            || $responseDto->body === null) {
            return null;
        }

        $computedChecksum = $this->getChecksum($responseDto->body);

        if ($externalFileDto->hash !== null && $computedChecksum !== $externalFileDto->hash) {
            return null;
        }

        if ($fileName === null) {
            $urlInfo = parse_url($externalFileDto->url);
            $fileName = basename($urlInfo['path']);
        }

        return $this->saveFile($fileName, $responseDto->body, $location, $computedChecksum);
    }

    /**
     * @param \Borlabs\Cookie\System\FileSystem\FileLocationInterface $location
     *
     * @throws \Borlabs\Cookie\Exception\GenericException
     */
    private function saveFile(string $fileName, string $fileContent, FileLocationInterface $location, ?string $computedChecksum = null): ?FileDto
    {
        if ($computedChecksum === null) {
            $computedChecksum = $this->getChecksum($fileContent);
        }

        if ($this->ensureFolderIsWritable($location) === false) {
            throw new GenericException('targetFolderNotWritable');
        }

        $filePath = $location->getPath() . '/' . $fileName;
        $saveStatus = WpFilesystem::getInstance()->put_contents($filePath, $fileContent);

        if (!$saveStatus) {
            $this->log->error(
                'Could not save file',
                [
                    'fileName' => $fileName,
                    'filePermission' => WpFilesystem::getInstance()->exists($filePath) ? WpFilesystem::getInstance()->getchmod($filePath) : '',
                    'locationPermission' => WpFilesystem::getInstance()->getchmod($location->getPath()),
                    'path' => $location->getPath(),
                ],
            );
        }

        return new FileDto(
            $fileName,
            $location->getPath(),
            $computedChecksum,
            $location->getUrl() . '/' . $fileName,
        );
    }
}
