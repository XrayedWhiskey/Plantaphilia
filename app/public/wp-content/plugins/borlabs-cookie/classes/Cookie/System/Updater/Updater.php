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

namespace Borlabs\Cookie\System\Updater;

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\ApiClient\PluginUpdateApiClient;
use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Enum\System\AutomaticUpdateEnum;
use Borlabs\Cookie\Exception\ApiClient\PluginUpdateApiClientException;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Exception\IncompatibleTypeException;
use Borlabs\Cookie\Support\Traits\VersionNumberTrait;
use Borlabs\Cookie\System\Config\PluginConfig;
use Borlabs\Cookie\System\Log\Log;
use Borlabs\Cookie\System\Option\Option;
use Exception;
use Plugin_Upgrader;
use stdClass;

class Updater
{
    use VersionNumberTrait;

    private Container $container;

    private Log $log;

    private Option $option;

    private PluginConfig $pluginConfig;

    private PluginUpdateApiClient $pluginUpdateApiClient;

    private WpDb $wpdb;

    private WpFunction $wpFunction;

    public function __construct(
        Container $container,
        Log $log,
        Option $option,
        PluginConfig $pluginConfig,
        PluginUpdateApiClient $pluginUpdateApiClient,
        WpDb $wpdb,
        WpFunction $wpFunction
    ) {
        $this->container = $container;
        $this->log = $log;
        $this->option = $option;
        $this->pluginConfig = $pluginConfig;
        $this->pluginUpdateApiClient = $pluginUpdateApiClient;
        $this->wpdb = $wpdb;
        $this->wpFunction = $wpFunction;
    }

    public function fileUpdateComplete($wpUpgraderInstance, $itemUpdateData)
    {
        if ($wpUpgraderInstance instanceof Plugin_Upgrader === false) {
            return;
        }

        if (!isset($wpUpgraderInstance->result['source_files']) || !in_array(basename(BORLABS_COOKIE_BASENAME), $wpUpgraderInstance->result['source_files'], true)) {
            return;
        }

        $this->processUpdate();
    }

    public function getLatestVersion($transient)
    {
        // Skip the API request if the transient contains the plugin data
        if (isset($transient->response[BORLABS_COOKIE_BASENAME])) {
            return $transient;
        }

        $latestPluginVersion = null;

        try {
            $latestPluginVersion = $this->pluginUpdateApiClient->requestLatestPluginVersion();
        } catch (IncompatibleTypeException $e) {
            $this->log->error('IncompatibleTypeException in Updater', [
                'message' => $e->getMessage(),
                'context' => $e->getContext(),
            ]);
        } catch (PluginUpdateApiClientException $e) {
            $this->log->critical('PluginUpdateApiClientException in Updater', [
                'message' => $e->getMessage(),
            ]);
        } catch (GenericException $e) {
            $this->log->warning('Exception in Updater', [
                'message' => $e->getMessage(),
            ]);
        }

        if ($latestPluginVersion === null) {
            return $transient;
        }

        // Skip, when the current version is already the latest version
        $currentVersion = $this->transformToVersionNumberWithHotfixDto(BORLABS_COOKIE_VERSION);
        $newVersion = $this->transformToVersionNumberWithHotfixDto($latestPluginVersion->new_version ?? '0.0.0.0');

        if (
            is_null($latestPluginVersion->new_version)
            || $this->compareVersionNumber($currentVersion, $newVersion, '>=')
        ) {
            return $transient;
        }

        // $transient can be null if third-party plugins force a plugin refresh an kill the object
        if (!is_object($transient) && !isset($transient->response)) {
            $transient = new stdClass();
            $transient->response = [];
        }

        /*
         * Casting our Dto to a stdClass as required by WordPress.
         * We could also pass our Dto instead, but there might be future versions of WordPress that require a stdClass.
         */
        $transient->response[BORLABS_COOKIE_BASENAME] = (object) (array) $latestPluginVersion;

        return $transient;
    }

    public function getPluginInformation($result, $action, $args)
    {
        if (!isset($action) || $action !== 'plugin_information') {
            return $result;
        }

        if ($args->slug !== BORLABS_COOKIE_SLUG) {
            return $result;
        }

        try {
            $pluginInformation = $this->pluginUpdateApiClient->requestPluginInformation();
        } catch (IncompatibleTypeException $e) {
            $this->log->error('IncompatibleTypeException in Updater', [
                'message' => $e->getMessage(),
                'context' => $e->getContext(),
            ]);
        } catch (PluginUpdateApiClientException $e) {
            $this->log->critical('PluginUpdateApiClientException in Updater', [
                'message' => $e->getMessage(),
            ]);
        } catch (GenericException $e) {
            $this->log->warning('Exception in Updater', [
                'message' => $e->getMessage(),
            ]);
        }

        if (!isset($pluginInformation)) {
            return $result;
        }

        return (object) (array) $pluginInformation;
    }

    public function handleAutomaticUpdateStatus()
    {
        $autoUpdatePluginsList = $this->option->getThirdPartyOption('auto_update_plugins', []);
        $shouldUpdateOption = false;

        if (!is_array($autoUpdatePluginsList->value)) {
            $autoUpdatePluginsList->value = [];
        }

        // Remove from auto_update_plugins list
        if ($this->pluginConfig->get()->automaticUpdate == AutomaticUpdateEnum::AUTO_UPDATE_NONE()) {
            if (in_array(BORLABS_COOKIE_BASENAME, $autoUpdatePluginsList->value, true)) {
                $index = array_search(BORLABS_COOKIE_BASENAME, $autoUpdatePluginsList->value, true);

                if ($index !== false) {
                    unset($autoUpdatePluginsList->value[$index]);
                    sort($autoUpdatePluginsList->value);
                    $shouldUpdateOption = true;
                }
            }
        } else {
            if (!in_array(BORLABS_COOKIE_BASENAME, $autoUpdatePluginsList->value, true)) {
                $autoUpdatePluginsList->value[] = BORLABS_COOKIE_BASENAME;
                $shouldUpdateOption = true;
            }
        }

        // Update WordPress auto_update_plugins option
        if ($shouldUpdateOption) {
            $this->option->setThirdPartyOption('auto_update_plugins', $autoUpdatePluginsList->value);
        }
    }

    public function processUpdate()
    {
        $blogId = $this->wpFunction->getCurrentBlogId();
        $prefix = $this->wpdb->prefix;

        try {
            $this->container->get('Borlabs\Cookie\System\Installer\MigrationService')->run($prefix);
        } catch (Exception $e) {
            error_log($e->getMessage());
        }

        if (!$this->wpFunction->isMultisite()) {
            return;
        }

        $sites = $this->wpFunction->getSites();

        if (count($sites) === 0) {
            return;
        }

        foreach ($sites as $site) {
            if ($site->blog_id !== 1) {
                $this->wpFunction->switchToBlog((int) $site->blog_id);
                $prefix = $this->wpdb->prefix;

                try {
                    $this->container->get('Borlabs\Cookie\System\Installer\MigrationService')->run($prefix);
                    $this->log->info(
                        'Migration service executed for the instance: {{ blogId }}',
                        [
                            'blogId' => $site->blog_id,
                            'prefix' => $prefix,
                        ],
                    );
                } catch (Exception $e) {
                    error_log($e->getMessage());
                }
            }
            $this->wpFunction->switchToBlog($blogId);
        }
    }

    public function register()
    {
        $this->wpFunction->addAction(
            'plugins_api',
            [$this, 'getPluginInformation'],
            9001,
            3,
        );
        $this->wpFunction->addAction(
            'pre_set_site_transient_update_plugins',
            [$this, 'getLatestVersion'],
        );
    }

    /**
     * @param null|object $itemUpdateData this value is never null; however, due to a PHP deprecation warning, we need to permit null values.
     *                                    The type of $itemUpdateData can also be arbitrary due to third-party plugins that can interfere with the update process.
     */
    public function shouldApplyAutoUpdate(?bool $update = null, $itemUpdateData = null): ?bool
    {
        if (is_null($itemUpdateData) || !is_object($itemUpdateData)) {
            return $update;
        }

        if (!isset($itemUpdateData->slug) || $itemUpdateData->slug !== BORLABS_COOKIE_SLUG) {
            return $update;
        }

        if ($this->pluginConfig->get()->automaticUpdate->is(AutomaticUpdateEnum::AUTO_UPDATE_NONE())) {
            return false;
        }

        if ($this->pluginConfig->get()->automaticUpdate->is(AutomaticUpdateEnum::AUTO_UPDATE_ALL())) {
            return true;
        }

        $currentVersion = BORLABS_COOKIE_VERSION;
        $currentVersionParts = explode('.', $currentVersion); // Example: '3.0.10.4' => ['3', '0', '10', '4']
        $newVersionParts = explode('.', $itemUpdateData->new_version); // Example: '3.0.11' => ['3', '0', '11']

        if ($this->pluginConfig->get()->automaticUpdate->is(AutomaticUpdateEnum::AUTO_UPDATE_MINOR())) {
            return $currentVersionParts[0] === $newVersionParts[0];
        }

        if ($this->pluginConfig->get()->automaticUpdate->is(AutomaticUpdateEnum::AUTO_UPDATE_PATCH())) {
            return $currentVersionParts[0] === $newVersionParts[0] && $currentVersionParts[1] === $newVersionParts[1];
        }

        return false;
    }
}
