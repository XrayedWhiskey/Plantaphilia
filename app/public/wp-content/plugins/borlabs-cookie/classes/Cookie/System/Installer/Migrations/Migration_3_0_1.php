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

namespace Borlabs\Cookie\System\Installer\Migrations;

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Repository\Service\ServiceOptionRepository;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Log\Log;
use Borlabs\Cookie\System\Script\ScriptConfigBuilder;
use Borlabs\Cookie\System\Style\StyleBuilder;
use Borlabs\Cookie\System\ThirdPartyCacheClearer\ThirdPartyCacheClearerManager;

class Migration_3_0_1
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function run()
    {
        $language = $this->container->get(Language::class);
        $wpFunction = $this->container->get(WpFunction::class);
        $status = $this->container->get(ScriptConfigBuilder::class)->updateJavaScriptConfigFileAndIncrementConfigVersion(
            $language->getSelectedLanguageCode(),
        );

        $migrationStatus = $this->migrateServiceOptionEnumFromLocationProcessingToProcessingLocation();
        $this->container->get(Log::class)->info(
            sprintf(
                'Service Option Enum migrated: %s',
                $migrationStatus ? 'Yes' : 'No',
            ),
            [
                'migrationStatus' => $migrationStatus,
            ],
        );

        $this->container->get(Log::class)->info(
            sprintf('JavaScript config file updated: %s', $status ? 'Yes' : 'No', ),
            [
                'language' => $language->getSelectedLanguageCode(),
            ],
        );

        $status = $this->container->get(StyleBuilder::class)->updateCssFileAndIncrementStyleVersion(
            $wpFunction->getCurrentBlogId(),
            $language->getSelectedLanguageCode(),
        );

        $this->container->get(Log::class)->info(
            sprintf('CSS file updated: %s', $status ? 'Yes' : 'No'),
            [
                'blogId' => $wpFunction->getCurrentBlogId(),
                'language' => $language->getSelectedLanguageCode(),
            ],
        );

        $this->container->get(ThirdPartyCacheClearerManager::class)->clearCache();

        // Special case for WP Rocket, as only rocket_clean_minify() clears the asset cache.
        if (function_exists('rocket_clean_minify')) {
            rocket_clean_minify();

            $this->container->get(Log::class)->info('WP Rocket asset cache cleared.');
        }
    }

    private function migrateServiceOptionEnumFromLocationProcessingToProcessingLocation(): bool
    {
        $wpdb = $this->container->get(WpDb::class);
        $prefix = $wpdb->prefix;

        return $wpdb->query('
            UPDATE
                ' . $prefix . ServiceOptionRepository::TABLE . '
            SET
                `type` = \'processing_location\'
            WHERE
                `type` = \'location_processing\'
        ') !== false;
    }
}
