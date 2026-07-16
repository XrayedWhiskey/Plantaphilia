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

namespace Borlabs\Cookie\System\CompatibilityPatch;

use Borlabs\Cookie\Container\ApplicationContainer;
use Borlabs\Cookie\Dto\System\FileDto;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Model\CompatibilityPatch\CompatibilityPatchModel;
use Borlabs\Cookie\Repository\CompatibilityPatch\CompatibilityPatchRepository;
use Borlabs\Cookie\Support\Formatter;
use Borlabs\Cookie\System\FileSystem\FileManager;
use Borlabs\Cookie\System\FileSystem\StorageFolder;
use Borlabs\Cookie\System\LocalScanner\ScanRequestService;
use Borlabs\Cookie\System\Log\Log;
use Borlabs\Cookie\System\SafeMode\SafeMode;

final class CompatibilityPatchManager
{
    private ApplicationContainer $applicationContainer;

    private array $compatibilityPatchInstances = [];

    private array $compatibilityPatchInstancesWithSkipInitializationCheck = [];

    private CompatibilityPatchRepository $compatibilityPatchRepository;

    private FileManager $fileManager;

    private Log $log;

    private SafeMode $safeMode;

    private ScanRequestService $scanRequestService;

    private StorageFolder $storageFolder;

    public function __construct(
        ApplicationContainer $applicationContainer,
        CompatibilityPatchRepository $compatibilityPatchRepository,
        FileManager $fileManager,
        Log $log,
        SafeMode $safeMode,
        ScanRequestService $scanRequestService,
        StorageFolder $storageFolder
    ) {
        $this->applicationContainer = $applicationContainer;
        $this->compatibilityPatchRepository = $compatibilityPatchRepository;
        $this->fileManager = $fileManager;
        $this->log = $log;
        $this->safeMode = $safeMode;
        $this->scanRequestService = $scanRequestService;
        $this->storageFolder = $storageFolder;
    }

    /**
     * @throws \Borlabs\Cookie\Exception\GenericException
     *
     * @return \Borlabs\Cookie\Dto\System\FileDto
     */
    public function getPatchFile(CompatibilityPatchModel $compatibilityPatchModel): ?FileDto
    {
        return $this->fileManager->getStoredFile($compatibilityPatchModel->fileName, true);
    }

    public function initPatches(): void
    {
        foreach ($this->compatibilityPatchInstances as $patch) {
            $patch->init();
        }
    }

    public function loadPatches(): void
    {
        if ($this->safeMode->isEnabled()) {
            return;
        }

        if ($this->scanRequestService->noCompatibilityPatches()) {
            return;
        }

        if ($this->loadPatchesInDevelopment()) {
            return;
        }

        $compatibilityPatches = $this->compatibilityPatchRepository->getAll();

        foreach ($compatibilityPatches as $patch) {
            $patchFile = $this->storageFolder->getPath() . '/' . $patch->fileName;

            if (file_exists($patchFile)) {
                define('BORLABS_COOKIE_COMPATIBILITY_PATCH_' . strtoupper($patch->fileName), true);
                $className = 'BorlabsCookieCompatibilityPatch' . Formatter::toPascalCase($patch->key);

                require_once $patchFile;

                $this->handleInstancing($className);
            }
        }
    }

    public function shouldSkipInitialization(): bool
    {
        foreach ($this->compatibilityPatchInstancesWithSkipInitializationCheck as $patch) {
            if ($patch->shouldSkipInitialization()) {
                $this->log->debug('Borlabs Cookie initialization skipped.', ['patch' => get_class($patch)]);

                return true;
            }
        }

        return false;
    }

    public function validatePatch(CompatibilityPatchModel $compatibilityPatchModel): bool
    {
        try {
            $patchFile = $this->getPatchFile($compatibilityPatchModel);

            return $patchFile && $patchFile->hash === $compatibilityPatchModel->hash;
        } catch (GenericException $e) {
            return false;
        }
    }

    private function handleInstancing($className): bool
    {
        if (class_exists($className) && method_exists($className, 'init')) {
            $instance = new $className($this->applicationContainer);
            $this->compatibilityPatchInstances[] = $instance;

            if (method_exists($className, 'shouldSkipInitialization')) {
                $this->compatibilityPatchInstancesWithSkipInitializationCheck[] = $instance;
            }

            return true;
        }

        return false;
    }

    private function loadPatchesInDevelopment(): bool
    {
        $flagEnableAllCompatibilityPatches = defined('BORLABS_COOKIE_DEV_MODE_ENABLE_ALL_COMPATIBILITY_PATCHES') && constant('BORLABS_COOKIE_DEV_MODE_ENABLE_ALL_COMPATIBILITY_PATCHES') === true;
        $flagEnableSpecificCompatibilityPatches = defined('BORLABS_COOKIE_DEV_MODE_ENABLE_SPECIFIC_COMPATIBILITY_PATCHES') && is_array($compatibilityPatches = constant('BORLABS_COOKIE_DEV_MODE_ENABLE_SPECIFIC_COMPATIBILITY_PATCHES')) && count($compatibilityPatches) > 0;

        if (!$flagEnableAllCompatibilityPatches && !$flagEnableSpecificCompatibilityPatches) {
            return false;
        }

        $directory = BORLABS_COOKIE_PLUGIN_PATH . '/compatibility-patches-development/';
        $patchFiles = glob($directory . '*.php');

        foreach ($patchFiles as $patchFile) {
            $className = basename($patchFile, '.php');

            if ($flagEnableSpecificCompatibilityPatches && !in_array($className, $compatibilityPatches, true)) {
                continue;
            }

            require $patchFile;

            $this->handleInstancing($className);
        }

        return true;
    }
}
