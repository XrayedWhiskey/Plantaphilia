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

namespace Borlabs\Cookie\System\ThirdPartyImporter\BorlabsCookieLegacy;

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Dto\Package\InstallationStatusDto;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\Dto\ThirdPartyImporter\PreImportMetadataDto;
use Borlabs\Cookie\DtoList\Package\InstallationStatusDtoList;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\DtoList\ThirdPartyImporter\PreImportMetadataDtoList;
use Borlabs\Cookie\Enum\Package\ComponentTypeEnum;
use Borlabs\Cookie\Enum\Package\InstallationStatusEnum;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Model\ScriptBlocker\ScriptBlockerModel;
use Borlabs\Cookie\Repository\ScriptBlocker\ScriptBlockerRepository;
use Borlabs\Cookie\Support\Database;
use Borlabs\Cookie\Support\Formatter;
use Borlabs\Cookie\System\Log\Log;

final class ScriptBlockerImporter
{
    public const TABLE_NAME = 'borlabs_cookie_legacy_script_blocker';

    private Log $log;

    private ScriptBlockerRepository $scriptBlockerRepository;

    private WpDb $wpdb;

    public function __construct(
        Log $log,
        ScriptBlockerRepository $scriptBlockerRepository,
        WpDb $wpdb
    ) {
        $this->log = $log;
        $this->scriptBlockerRepository = $scriptBlockerRepository;
        $this->wpdb = $wpdb;
    }

    public function getPreImportMetadataList(): PreImportMetadataDtoList
    {
        $preImportMetadataList = new PreImportMetadataDtoList(null);
        $scriptBlockers = $this->getScriptBlockers();

        if ($scriptBlockers === null) {
            return $preImportMetadataList;
        }

        foreach ($scriptBlockers as $scriptBlocker) {
            $preImportMetadataList->add(
                new PreImportMetadataDto(
                    \Borlabs\Cookie\Enum\ThirdPartyImporter\ComponentTypeEnum::SCRIPT_BLOCKER(),
                    $scriptBlocker->name,
                    $scriptBlocker->script_blocker_id,
                    $scriptBlocker->script_blocker_id,
                    null,
                ),
            );
        }

        return $preImportMetadataList;
    }

    public function importCustom(): InstallationStatusDtoList
    {
        $installationStatusList = new InstallationStatusDtoList(null);

        $legacyScriptBlockers = $this->getScriptBlockers();

        if (!isset($legacyScriptBlockers)) {
            return $installationStatusList;
        }

        /**
         * @var object{
         *     handles: string,
         *     js_block_phrases: string,
         *     name: string,
         *     script_blocker_id: string,
         *     status: string
         *     } $legacyScriptBlocker
         */
        foreach ($legacyScriptBlockers as $legacyScriptBlocker) {
            $legacyScriptBlocker->handles = unserialize($legacyScriptBlocker->handles);
            $legacyScriptBlocker->js_block_phrases = unserialize($legacyScriptBlocker->js_block_phrases);

            $this->log->info(
                '[Import] Script Blocker "{{ scriptBlockerName }}"',
                [
                    'legacyScriptBlocker' => $legacyScriptBlocker,
                    'scriptBlockerName' => $legacyScriptBlocker->name,
                ],
            );

            $scriptBlocker = $this->getOrAddScriptBlockerFromLegacyData($legacyScriptBlocker);
            $installationStatusList->add(
                new InstallationStatusDto(
                    $scriptBlocker ? InstallationStatusEnum::SUCCESS() : InstallationStatusEnum::FAILURE(),
                    ComponentTypeEnum::SCRIPT_BLOCKER(),
                    $legacyScriptBlocker->script_blocker_id,
                    $legacyScriptBlocker->name,
                    $scriptBlocker->id ?? -1,
                ),
            );
        }

        return $installationStatusList;
    }

    /**
     * @param object{
     *     handles: array,
     *     js_block_phrases: array,
     *     name: string,
     *     script_blocker_id: string,
     *     status: string
     *     } $legacyScriptBlockerData
     */
    private function getOrAddScriptBlockerFromLegacyData(object $legacyScriptBlockerData): ?ScriptBlockerModel
    {
        $existingModel = $this->scriptBlockerRepository->getByKey(
            $legacyScriptBlockerData->script_blocker_id,
        );

        if ($existingModel !== null) {
            return $existingModel;
        }

        $newModel = new ScriptBlockerModel();
        $newModel->handles = new KeyValueDtoList();
        $newModel->key = $legacyScriptBlockerData->script_blocker_id;
        $newModel->name = $legacyScriptBlockerData->name;
        $newModel->onExist = new KeyValueDtoList();
        $newModel->phrases = new KeyValueDtoList();
        $newModel->status = (bool) $legacyScriptBlockerData->status;

        // Add handles and phrases
        array_walk($legacyScriptBlockerData->handles, function ($value, $key) use ($newModel) {
            $newModel->handles->add(new KeyValueDto($key, ''));
        });
        array_walk($legacyScriptBlockerData->js_block_phrases, function ($value) use ($newModel) {
            $newModel->phrases->add(new KeyValueDto(Formatter::toCamelCase($value), trim($value)));
        });

        $scriptBlockerModel = null;

        try {
            $scriptBlockerModel = $this->scriptBlockerRepository->insert($newModel);
        } catch (GenericException $e) {
            $this->log->error('[Import] Failed to insert Script Blocker.', ['exception' => $e]);
        }

        return $scriptBlockerModel;
    }

    private function getScriptBlockers(): ?array
    {
        if (!Database::tableExists($this->wpdb->prefix . self::TABLE_NAME)) {
            return null;
        }

        $scriptBlockers = $this->wpdb->get_results('
            SELECT
                `handles`,
                `js_block_phrases`,
                `name`,
                `script_blocker_id`,
                `status`
            FROM
                `' . $this->wpdb->prefix . self::TABLE_NAME . '`
        ');

        if (!is_array($scriptBlockers) || count($scriptBlockers) === 0) {
            return null;
        }

        return $scriptBlockers;
    }
}
