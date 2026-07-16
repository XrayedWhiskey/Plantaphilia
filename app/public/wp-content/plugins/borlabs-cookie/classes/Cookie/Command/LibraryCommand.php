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

namespace Borlabs\Cookie\Command;

use Borlabs\Cookie\ApiClient\PackageApiClient;
use Borlabs\Cookie\ApiClient\Transformer\PackageTransformer;
use Borlabs\Cookie\ApiClient\Transformer\ServiceTransformer;
use Borlabs\Cookie\Container\ApplicationContainer;
use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Enum\Package\InstallationStatusEnum;
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\Model\Package\PackageModel;
use Borlabs\Cookie\Repository\Package\PackageRepository;
use Borlabs\Cookie\System\Package\PackageManager;
use JsonException;
use Throwable;
use WP_CLI;

/**
 * Manages the Borlabs Cookie plugin.
 */
class LibraryCommand extends AbstractCommand
{
    /**
     * @const DEFAULT_FIELDS Default fields to display for each object.
     */
    public const DEFAULT_FIELDS = [
        'id',
        'name',
    ];

    private Container $container;

    private PackageManager $packageManager;

    private PackageRepository $packageRepository;

    /**
     * SystemCommand constructor.
     */
    public function __construct()
    {
        $this->container = ApplicationContainer::get();
        $this->packageRepository = $this->container->get(PackageRepository::class);
        $this->packageManager = $this->container->get(PackageManager::class);
    }

    /**
     * Get config template for package.
     *
     * ## OPTIONS
     *
     *  <package>
     *  : The key of the package to install.
     *
     *  <languages>
     *  : The languages the package should be installed in.
     *
     * ## EXAMPLES
     *
     *    # Get config template for package.
     *    $ wp borlabs-cookie library config-template 1 de,en
     *
     * @subcommand config-template
     */
    public function configTemplate(array $args, array $assocArgs)
    {
        try {
            $packageId = (int) ($args[0]);
            $package = $this->packageRepository->findById($packageId);
            $languages = $this->parseArgumentLanguages($args);

            if ($package === null) {
                WP_CLI::error('Cannot find package with id=' . $packageId);

                return;
            }

            $config = (object) [];
            $packageData = $this->container->get(PackageApiClient::class)->requestPackage($package->borlabsServicePackageKey);

            foreach ($languages as $language) {
                $tempConfig = [];

                foreach ($packageData->data->components->contentBlockers as $contentBlocker) {
                    $tempContentBlockerConfig = [
                        'overwrite-translation' => false,
                    ];

                    foreach ($contentBlocker->settingsFields as $settingsField) {
                        $tempContentBlockerConfig[$settingsField->key] = $settingsField->defaultValue;
                    }
                    $tempConfig['contentBlockers'][$contentBlocker->key] = $tempContentBlockerConfig;
                }

                foreach ($packageData->data->components->services as $service) {
                    $serviceModel = $this->container->get(ServiceTransformer::class)->toModel($service, $service->key, $language);
                    $tempServiceConfig = [
                        'overwrite-translation' => false,
                    ];

                    foreach ($serviceModel->settingsFields->list as $settingsField) {
                        $tempServiceConfig[$settingsField->key] = $settingsField->defaultValue;
                    }
                    $tempConfig['services'][$service->key] = $tempServiceConfig;
                }
                $config->{$language} = (object) $tempConfig;
            }

            WP_CLI::line(json_encode($config));
            WP_CLI::success('Config template generated');
        } catch (Throwable $throwable) {
            WP_CLI::line($throwable->getMessage());
            WP_CLI::line($throwable->getTraceAsString());

            return;
        }
    }

    /**
     * Installs one package.
     *
     * ## OPTIONS
     *
     * <package>
     * : The key of the package to install.
     *
     * <languages>
     * : The languages the package should be installed in.
     *
     * [--config=<config-as-json>]
     * : The configuration of the package as JSON.
     * You can use the command "wp borlabs-cookie library config-template" to get a template.
     *
     * ## EXAMPLES
     *
     *     # Install one package.
     *     $ wp borlabs-cookie library install 1 de,en
     *
     * @throws \WP_CLI\ExitException
     */
    public function install(array $args, array $assocArgs): void
    {
        try {
            $packageId = (int) ($args[0]);
            $package = $this->packageRepository->findById($packageId);
            $languages = $this->parseArgumentLanguages($args);

            if ($package === null) {
                WP_CLI::error('Cannot find package with id=' . $packageId);

                return;
            }

            if ($package->installedAt !== null) {
                WP_CLI::error('Package "' . $package->name . '" is already installed');

                return;
            }

            $config = [
                'language' => [],
            ];

            foreach ($languages as $language) {
                $config['language'][$language] = '1';
            }

            $configAttribute = WP_CLI\Utils\get_flag_value($assocArgs, 'config', null);

            if ($configAttribute !== null) {
                try {
                    $configAttributeJson = json_decode($configAttribute, true, 512, JSON_THROW_ON_ERROR);
                    $config['settingsForLanguage'] = $configAttributeJson;
                } catch (JsonException $jsonException) {
                    WP_CLI::error('Invalid JSON in config option: ' . $jsonException->getMessage());

                    return;
                }
            }

            $statusEntries = $this->packageManager->installWithCleanupAndTelemetry($package, $config);

            WP_CLI::line('Installation status:');
            $this->outputStatusEntries($statusEntries->list);

            WP_CLI::success('Installed package "' . $package->name . '"');
        } catch (TranslatedException $exception) {
            WP_CLI::line($exception->getTranslatedMessage());
        } catch (Throwable $throwable) {
            WP_CLI::line($throwable->getMessage());
            WP_CLI::line($throwable->getTraceAsString());

            return;
        }
    }

    /**
     * Gets a list of packages of the library.
     *
     * ## OPTIONS
     *
     * [--field=<field>]
     * : Prints the value of a single field for each package.
     * ---
     * options:
     *   - id
     *   - name
     *   - version
     *   - latest-version
     *   - is-installed
     *   - outdated
     * ---
     *
     * [--fields=<fields>]
     * : Limit the output to specific object fields.
     *
     *
     * [--format=<format>]
     * : Render output in a particular format.
     * ---
     * default: table
     * options:
     *   - table
     *   - csv
     *   - json
     *   - count
     *   - yaml
     * ---
     *
     * [--outdated[=<outdated>]]
     * : Filter package whether they need an update.
     * ---
     * options:
     *   - true
     *   - false
     * ---
     *
     * ## AVAILABLE FIELDS
     *
     * These fields will be displayed by default for each package:
     *
     * * id
     * * name
     *
     * These fields are optionally available:
     *
     * * version
     *
     * ## EXAMPLES
     *
     *     # List the ids of all packages
     *     $ wp borlabs-cookie library list --field=id
     *     10
     *     11
     *     12
     *     13
     *
     *     # List one field of packages in JSON
     *     $ wp borlabs-cookie library list --field=id --format=json
     *     [10,11,12,13]
     *
     *     # List all packages in a table
     *     $ wp borlabs-cookie library list --fields=id,name,version
     *     | id | name                     | version |
     *     +----+--------------------------+---------+
     *     | 10 | Userlike                 | 1.0.0   |
     *     | 11 | Contact Form 7           | 1.0.0   |
     *     | 12 | WP Go Maps               | 1.0.0   |
     *     | 13 | Google Maps              | 1.1.0   |
     *     +----+--------------------------+---------+
     */
    public function list(array $args, array $assocArgs): void
    {
        $formatter = $this->getFormatter($assocArgs, self::DEFAULT_FIELDS);
        $format = $formatter->__get('format') ?? 'table';

        $packages = $this->packageRepository->find();

        if (array_key_exists('outdated', $assocArgs)) {
            $outdatedFilterValue = WP_CLI\Utils\get_flag_value($assocArgs, 'outdated', true);

            if ($outdatedFilterValue === 'true' || $outdatedFilterValue === true) {
                $outdatedFilter = true;
            } elseif ($outdatedFilterValue === 'false') {
                $outdatedFilter = false;
            } else {
                WP_CLI::error('Flag --outdated has invalid value');

                return;
            }

            $packages = array_filter($packages, function (PackageModel $packageModel) use ($outdatedFilter): bool {
                $outdated = $packageModel->installedAt !== null && $packageModel->version !== $packageModel->borlabsServicePackageVersion;

                return $outdatedFilter ? $outdated : !$outdated;
            });
        }

        $iterator = WP_CLI\Utils\iterator_map(
            $packages,
            function (PackageModel $packageModel) use ($format) {
                return $this->mapToCliTable($packageModel, $format);
            },
        );

        $formatter->display_items($iterator);
    }

    /**
     * Uninstall one package.
     *
     * ## OPTIONS
     *
     * <package>
     * : The key of the package to uninstall.
     *
     * <languages>
     * : The languages the package should be installed in.
     *
     * ## EXAMPLES
     *
     *     # Uninstall one package.
     *     $ wp borlabs-cookie library uninstall 1 de,en
     *
     * @throws \WP_CLI\ExitException
     */
    public function uninstall(array $args, array $assocArgs): void
    {
        $packageId = (int) ($args[0]);
        $package = $this->packageRepository->findById($packageId);
        $languages = explode(',', (string) $args[1]);

        if ($package === null) {
            WP_CLI::error('Cannot find package with id=' . $packageId);

            return;
        }

        if ($package->installedAt === null) {
            WP_CLI::error('Package "' . $package->name . '" not installed');

            return;
        }

        $config = [
            'language' => [],
        ];

        foreach ($languages as $language) {
            $config['language'][$language] = '1';
        }

        try {
            $statusEntries = $this->packageManager->uninstall($package, $config);
            WP_CLI::line('Uninstallation status:');
            $this->outputStatusEntries($statusEntries);
        } catch (Throwable $throwable) {
            WP_CLI::error($throwable->getMessage());

            return;
        }

        WP_CLI::success('Uninstalled package "' . $package->name . '"');
    }

    /**
     * Update one package.
     *
     * ## OPTIONS
     *
     * <package>
     * : The key of the package to update.
     *
     * <languages>
     * : The languages the package should be updated in.
     *
     * [--config=<config-as-json>]
     * : The configuration of the package as JSON.
     * You can use the command "wp borlabs-cookie library config-template" to get a template.
     *
     * ## EXAMPLES
     *
     *     # Update one package.
     *     $ wp borlabs-cookie library update 1 de,en
     *
     * @throws \WP_CLI\ExitException
     */
    public function update(array $args, array $assocArgs): void
    {
        try {
            $packageId = (int) ($args[0]);
            $package = $this->packageRepository->findById($packageId, [
                'services',
                'contentBlockers',
                'compatibilityPatches',
                'scriptBlockers',
                'styleBlockers',
            ]);
            $languages = $this->parseArgumentLanguages($args);

            if ($package === null) {
                WP_CLI::error('Cannot find package with id=' . $packageId);

                return;
            }

            if ($package->installedAt === null) {
                WP_CLI::error('Package "' . $package->name . '" not installed');

                return;
            }

            $config = [
                'language' => [],
            ];

            foreach ($languages as $language) {
                $config['language'][$language] = '1';
            }

            $configAttribute = WP_CLI\Utils\get_flag_value($assocArgs, 'config', null);

            if ($configAttribute !== null) {
                try {
                    $configAttributeJson = json_decode($configAttribute, true, 512, JSON_THROW_ON_ERROR);
                    $config['settingsForLanguage'] = $configAttributeJson;
                } catch (JsonException $jsonException) {
                    WP_CLI::error('Invalid JSON in config option: ' . $jsonException->getMessage());

                    return;
                }
            }

            $statusEntries = $this->packageManager->installWithCleanupAndTelemetry($package, $config);
            WP_CLI::line('Update status:');
            $this->outputStatusEntries($statusEntries->list);

            WP_CLI::success('Installed package "' . $package->name . '"');
        } catch (Throwable $throwable) {
            WP_CLI::line($throwable->getMessage());
            WP_CLI::line($throwable->getTraceAsString());

            return;
        }
    }

    /**
     * Refreshes or initially loads the package list of the Borlabs Cookie library.
     *
     * ## EXAMPLES
     *
     *     # Update package list
     *     $ wp borlabs-cookie library update-package-list
     *
     * @subcommand update-package-list
     */
    public function updatePackageList(array $args, array $assocArgs): void
    {
        try {
            $this->container->get(PackageManager::class)->updatePackageList();

            WP_CLI::success('Updated package list');
        } catch (Throwable $throwable) {
            WP_CLI::line($throwable->getMessage());
            WP_CLI::line($throwable->getTraceAsString());

            return;
        }
    }

    private function mapToCliTable(PackageModel $packageModel, string $format): array
    {
        return [
            'id' => $packageModel->id,
            'name' => $packageModel->name,
            'version' => $this->container->get(PackageTransformer::class)->versionNumberToString($packageModel->version),
            'latest-version' => $this->container->get(PackageTransformer::class)->versionNumberToString($packageModel->borlabsServicePackageVersion),
            'is-installed' => $this->formatBoolean($packageModel->installedAt !== null, $format),
            'outdated' => $this->formatBoolean($packageModel->installedAt !== null && $packageModel->version !== $packageModel->borlabsServicePackageVersion, $format),
        ];
    }

    /**
     * @param array<\Borlabs\Cookie\Dto\Package\InstallationStatusDto> $statusEntries
     */
    private function outputStatusEntries(array $statusEntries): void
    {
        foreach ($statusEntries as $statusEntry) {
            if ($statusEntry->status->is(InstallationStatusEnum::fromValue(InstallationStatusEnum::FAILURE))) {
                WP_CLI::line(' - Error: ' . $statusEntry->name . ' - ' . $statusEntry->key . ' - ' . $statusEntry->componentType->getDescription());
            } else {
                WP_CLI::line(' - Success: ' . $statusEntry->name . ' - ' . $statusEntry->key . ' - ' . $statusEntry->componentType->getDescription());
            }
        }
    }

    /**
     * @throws \WP_CLI\ExitException
     */
    private function parseArgumentLanguages(array $args): array
    {
        $languagesArg = explode(',', (string) $args[1]);
        $languages = [];

        foreach ($languagesArg as $languageArg) {
            $cleanedLanguage = trim($languageArg);

            if ($cleanedLanguage === '' || strlen($cleanedLanguage) !== 2) {
                WP_CLI::error('Invalid language code: "' . $cleanedLanguage . '"');

                return [];
            }
            $languages[] = $cleanedLanguage;
        }

        return $languages;
    }
}
