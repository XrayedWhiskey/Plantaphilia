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

namespace Borlabs\Cookie\Job;

use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Dto\Package\InstallationStatusDto;
use Borlabs\Cookie\Enum\Package\InstallationStatusEnum;
use Borlabs\Cookie\Enum\System\SettingsFieldVisibilityEnum;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Exception\HttpClient\ConnectionErrorException;
use Borlabs\Cookie\Exception\HttpClient\ServerErrorException;
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\Model\Job\JobModel;
use Borlabs\Cookie\Model\Package\PackageModel;
use Borlabs\Cookie\Repository\ContentBlocker\ContentBlockerRepository;
use Borlabs\Cookie\Repository\Package\PackageRepository;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\Repository\ServiceGroup\ServiceGroupRepository;
use Borlabs\Cookie\System\Log\Log;
use Borlabs\Cookie\System\Package\PackageAutoUpdateFailedMailJobService;
use Borlabs\Cookie\System\Package\PackageManager;
use Borlabs\Cookie\System\Package\Traits\SettingsFieldListTrait;

class PackageAutoUpdateJobHandler implements JobHandler
{
    use SettingsFieldListTrait;

    public const JOB_TYPE = 'packageAutoUpdate';

    public Log $log;

    private Container $container;

    private ContentBlockerRepository $contentBlockerRepository;

    private PackageAutoUpdateFailedMailJobService $packageAutoUpdateFailedMailJobService;

    private PackageRepository $packageRepository;

    private ServiceGroupRepository $serviceGroupRepository;

    private ServiceRepository $serviceRepository;

    public function __construct(
        Container $container,
        ContentBlockerRepository $contentBlockerRepository,
        Log $log,
        PackageAutoUpdateFailedMailJobService $packageAutoUpdateFailedMailJobService,
        PackageRepository $packageRepository,
        ServiceGroupRepository $serviceGroupRepository,
        ServiceRepository $serviceRepository
    ) {
        $this->container = $container;
        $this->contentBlockerRepository = $contentBlockerRepository;
        $this->log = $log;
        $this->packageAutoUpdateFailedMailJobService = $packageAutoUpdateFailedMailJobService;
        $this->packageRepository = $packageRepository;
        $this->serviceGroupRepository = $serviceGroupRepository;
        $this->serviceRepository = $serviceRepository;
    }

    public function handle(JobModel $job): void
    {
        // Ensure the package exists
        $package = $this->packageRepository->getByPackageKey($job->payload['borlabsServicePackageKey'], [
            'services',
            'contentBlockers',
            'compatibilityPatches',
            'scriptBlockers',
            'styleBlockers',
        ]);

        if ($package === null) {
            return;
        }

        $packageManager = $this->container->get(PackageManager::class);

        // Retrieve the installed components to identify which languages are configured and need to be updated.
        $installedContentBlockers = $this->getInstalledContentBlockers($package);
        $installedServices = $this->getInstalledServices($package);
        $languages = array_keys(
            array_merge_recursive($installedContentBlockers, $installedServices),
        );

        /**
         * Example:
         * <code>
         * [
         *     'language' => ['en' => '1', 'de' => '1', 'fr' => '1',]
         *     'settingsForLanguage' => [],
         * ]
         * </code>.
         *
         * @var \Borlabs\Cookie\Dto\Package\PackageDto $package
         */
        $componentSettings = [
            'language' => array_combine(
                $languages,
                array_map(fn () => '1', $languages),
            ),
            'settingsForLanguage' => [],
        ];

        /*
         * Merge the component settings with the custom settings to ensure that, for example, if the user
         * has selected a different service group, it is not overwritten by the package's default service group.
         * This data is required later when creating the component settings.
         */
        try {
            foreach ($package->components->contentBlockers->list as &$componentData) {
                $this->mergeComponentSettingsWithCustomSettings($componentData, $this->contentBlockerRepository);
            }

            foreach ($package->components->services->list as &$componentData) {
                $this->mergeComponentSettingsWithCustomSettings($componentData, $this->serviceRepository, $this->serviceGroupRepository);
            }
        } catch (TranslatedException $exception) {
            $this->log->error('Exception in PackageAutoUpdateJobHandler', [
                'exceptionMessage' => $exception->getTranslatedMessage(),
            ]);

            return;
        } catch (GenericException $exception) {
            $this->log->error('Generic exception in PackageAutoUpdateJobHandler', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return;
        }

        // If a Service or Content Blocker is added in an update for a language not supported by Borlabs Cookie, it must be added using the default English settings
        $this->ensureAllLanguagesInComponentSettings($package->components->contentBlockers->list, $languages);
        $this->ensureAllLanguagesInComponentSettings($package->components->services->list, $languages);

        // These settings are normally selected by the user in the backend in the details of the package.
        foreach ($languages as $language) {
            $componentSettings['settingsForLanguage'][$language] = [
                'contentBlocker' => [],
                'service' => [],
            ];

            foreach (['contentBlocker' => $installedContentBlockers[$language] ?? [], 'service' => $installedServices[$language] ?? []] as $type => $components) {
                foreach ($components as $component) {
                    $componentSettings['settingsForLanguage'][$language][$type][$component->key] = [
                        'overwrite-code' => $package->autoUpdateOverwriteCode ? '1' : '0',
                        'overwrite-translation' => $package->autoUpdateOverwriteTranslation ? '1' : '0',
                    ];
                }
            }
        }

        /*
         * The current configuration settings of this package are added to the component settings.
         * This, for example, ensures that the user's selected service group is not overwritten by the package's default service group.
         * Due to the way the package installation routine works, the information must be added to the component settings.
         */
        foreach (['contentBlocker' => $package->components->contentBlockers->list ?? [], 'service' => $package->components->services->list ?? []] as $type => $components) {
            foreach ($components as $component) {
                if (!isset($component->languageSpecificSetupSettingsFieldsList->list)) {
                    continue;
                }

                foreach ($component->languageSpecificSetupSettingsFieldsList->list as $languageSpecificSetupSettingsFields) {
                    $language = $languageSpecificSetupSettingsFields->language;

                    /**
                     * @var \Borlabs\Cookie\Dto\System\SettingsFieldDto $settingsField
                     */
                    foreach ($languageSpecificSetupSettingsFields->settingsFields->list as $settingsField) {
                        if ($settingsField->visibility->is(SettingsFieldVisibilityEnum::EDIT_ONLY())) {
                            continue;
                        }

                        $componentSettings['settingsForLanguage'][$language][$type][$component->key][$settingsField->key] = $settingsField->value;
                    }
                }
            }
        }

        $this->log->info('PackageAutoUpdateJobHandler: Updating package: {{ packageName }}', [
            'backup' => [
                'installedContentBlockers' => $installedContentBlockers,
                'installedServices' => $installedServices,
            ],
            'componentSettings' => $componentSettings,
            'package' => $package,
            'packageName' => $package->name,
        ]);

        $hasConnectionErrorException = false;
        $installationFailed = true;

        try {
            $installationStatus = $packageManager->installWithCleanupAndTelemetry($package, $componentSettings);
            $installationFailed = count(array_filter(
                $installationStatus->list ?? [],
                fn (InstallationStatusDto $installationStatus) => $installationStatus->status->is(InstallationStatusEnum::FAILURE()),
            ));
        } catch (ConnectionErrorException $e) {
            $hasConnectionErrorException = true;
            $this->log->error('Exception in PackageAutoUpdateJobHandler', [
                'exceptionMessage' => $e->getTranslatedMessage(),
            ]);
        } catch (ServerErrorException $e) {
            $hasConnectionErrorException = true;
            $this->log->error('Exception in PackageAutoUpdateJobHandler', [
                'exceptionMessage' => $e->getMessage(),
            ]);
        } catch (TranslatedException $e) {
            $this->log->error('Exception in PackageAutoUpdateJobHandler', [
                'exceptionMessage' => $e->getTranslatedMessage(),
            ]);
        } catch (GenericException $e) {
            $this->log->error('Generic exception in PackageAutoUpdateJobHandler', [
                'exceptionMessage' => $e->getMessage(),
            ]);
        }

        $this->log->info('PackageAutoUpdateJobHandler: Installation status: {{ status }}', [
            'installationStatus' => $installationStatus ?? null,
            'status' => $installationFailed ? 'FAILED' : 'SUCCESS',
        ]);

        if ($installationFailed && !$hasConnectionErrorException) {
            $this->packageAutoUpdateFailedMailJobService->updateJob([
                'borlabsServicePackageKey' => $job->payload['borlabsServicePackageKey'],
                'borlabsServicePackageVersion' => $package->borlabsServicePackageVersion,
                'processId' => $this->log->getProcessId(),
                'version' => $package->version,
            ]);
        }
    }

    private function ensureAllLanguagesInComponentSettings(array $components, array $requiredLanguages): void
    {
        foreach ($components as $component) {
            if (!isset($component->languageSpecificSetupSettingsFieldsList->list)) {
                continue;
            }

            // Get existing languages in this component
            $existingLanguages = [];

            foreach ($component->languageSpecificSetupSettingsFieldsList->list as $languageItem) {
                $existingLanguages[] = $languageItem->language;
            }

            // Find missing languages
            $missingLanguages = array_diff($requiredLanguages, $existingLanguages);

            if (empty($missingLanguages)) {
                continue; // All languages are present
            }

            // Find the English ('en') template to copy from
            $defaultSettings = null;

            foreach ($component->languageSpecificSetupSettingsFieldsList->list as $languageItem) {
                if ($languageItem->language === 'en') {
                    $defaultSettings = $languageItem;

                    break;
                }
            }

            if ($defaultSettings === null) {
                $this->log->warning('No English settings found for component: {{ componentKey }}', [
                    'componentKey' => $component->key ?? 'unknown',
                ]);

                continue;
            }

            // Create copies for missing languages
            foreach ($missingLanguages as $missingLanguage) {
                // Create a deep copy of the English template
                $newLanguageItem = clone $defaultSettings;
                $newLanguageItem->language = $missingLanguage;

                // Add the new language item to the service's list
                $component->languageSpecificSetupSettingsFieldsList->list[] = $newLanguageItem;

                $this->log->info('Added missing language {{ language }} to component {{ componentKey }} using English template', [
                    'language' => $missingLanguage,
                    'componentKey' => $component->key ?? 'unknown',
                ]);
            }
        }
    }

    private function getInstalledContentBlockers(PackageModel $package): array
    {
        $installedContentBlockers = [];

        foreach ($package->components->contentBlockers->list as $packageContentBlocker) {
            $contentBlockers = $this->contentBlockerRepository->getAllByKey($packageContentBlocker->key);

            foreach ($contentBlockers ?? [] as $contentBlocker) {
                $installedContentBlockers[$contentBlocker->language][$contentBlocker->key] = $contentBlocker;
            }
        }

        return $installedContentBlockers;
    }

    private function getInstalledServices(PackageModel $package): array
    {
        $installedServices = [];

        foreach ($package->components->services->list as $packageService) {
            $services = $this->serviceRepository->getAllByKey($packageService->key);

            foreach ($services ?? [] as $service) {
                $installedServices[$service->language][$service->key] = $service;
            }
        }

        return $installedServices;
    }
}
