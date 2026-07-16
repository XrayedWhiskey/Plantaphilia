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
use Borlabs\Cookie\Dto\ThirdPartyImporter\PreImportMetadataDto;
use Borlabs\Cookie\DtoList\ThirdPartyImporter\PreImportMetadataDtoList;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Model\ServiceGroup\ServiceGroupModel;
use Borlabs\Cookie\Repository\ServiceGroup\ServiceGroupRepository;
use Borlabs\Cookie\Support\Database;
use Borlabs\Cookie\System\Log\Log;

final class ServiceGroupImporter
{
    public const TABLE_NAME = 'borlabs_cookie_legacy_groups';

    private Log $log;

    /**
     * @var string[] BorlabsCookieLegacyGroupId => BorlabsCookieServiceGroupKey
     */
    private $map = [
        'essential' => 'essential',
        'external-media' => 'external-media',
        'marketing' => 'marketing',
        'statistics' => 'statistics',
    ];

    private ServiceGroupRepository $serviceGroupRepository;

    private WpDb $wpdb;

    public function __construct(
        Log $log,
        ServiceGroupRepository $serviceGroupRepository,
        WpDb $wpdb
    ) {
        $this->log = $log;
        $this->serviceGroupRepository = $serviceGroupRepository;
        $this->wpdb = $wpdb;
    }

    public function getPreImportMetadataList(): PreImportMetadataDtoList
    {
        $preImportMetadataList = new PreImportMetadataDtoList(null);
        $serviceGroups = $this->getServiceGroups();

        if ($serviceGroups === null) {
            return $preImportMetadataList;
        }

        foreach ($serviceGroups as $serviceGroup) {
            $preImportMetadataList->add(
                new PreImportMetadataDto(
                    \Borlabs\Cookie\Enum\ThirdPartyImporter\ComponentTypeEnum::SERVICE_GROUP(),
                    $serviceGroup->name,
                    $serviceGroup->group_id,
                    $serviceGroup->group_id,
                    $serviceGroup->language,
                ),
            );
        }

        return $preImportMetadataList;
    }

    public function import()
    {
        $this->importPreset();
        $this->importCustom();
    }

    public function importCustom(): ?bool
    {
        $legacyServiceGroups = $this->getServiceGroups();

        if (!isset($legacyServiceGroups)) {
            return null;
        }

        foreach ($legacyServiceGroups as $legacyServiceGroup) {
            // Skip default service groups
            if (isset($this->map[$legacyServiceGroup->group_id])) {
                continue;
            }

            $serviceGroup = $this->serviceGroupRepository->getByKey(
                $legacyServiceGroup->group_id,
                $legacyServiceGroup->language,
            );

            $serviceGroupModel = new ServiceGroupModel();

            if ($serviceGroup !== null) {
                $serviceGroupModel->id = $serviceGroup->id;
            }

            $serviceGroupModel->description = $legacyServiceGroup->description;
            $serviceGroupModel->key = $legacyServiceGroup->group_id;
            $serviceGroupModel->language = $legacyServiceGroup->language;
            $serviceGroupModel->name = $legacyServiceGroup->name;
            $serviceGroupModel->position = (int) $legacyServiceGroup->position;
            $serviceGroupModel->preSelected = false;
            $serviceGroupModel->status = (bool) $legacyServiceGroup->status;
            $serviceGroupModel->undeletable = false;

            try {
                if ($serviceGroup !== null) {
                    $this->serviceGroupRepository->update($serviceGroupModel);
                } else {
                    $this->serviceGroupRepository->insert($serviceGroupModel);
                }
            } catch (GenericException $e) {
                $this->log->error('[Import] Failed to insert/update Service Group.', ['exception' => $e]);
            }
        }

        return true;
    }

    public function importPreset(): bool
    {
        foreach ($this->map as $legacyServiceGroupKey => $serviceGroupKey) {
            $legacyServiceGroups = $this->getServiceGroupByKey($legacyServiceGroupKey);

            if (!isset($legacyServiceGroups)) {
                continue;
            }

            foreach ($legacyServiceGroups as $legacyServiceGroup) {
                $serviceGroup = $this->serviceGroupRepository->getByKey(
                    $legacyServiceGroup->group_id,
                    $legacyServiceGroup->language,
                );

                $serviceGroupModel = new ServiceGroupModel();

                if ($serviceGroup !== null) {
                    $serviceGroupModel->id = $serviceGroup->id;
                }

                $serviceGroupModel->description = $legacyServiceGroup->description;
                $serviceGroupModel->key = $legacyServiceGroup->group_id;
                $serviceGroupModel->language = $legacyServiceGroup->language;
                $serviceGroupModel->name = $legacyServiceGroup->name;
                $serviceGroupModel->position = (int) $legacyServiceGroup->position;
                $serviceGroupModel->preSelected = $legacyServiceGroup->group_id === 'essential';
                $serviceGroupModel->status = (bool) $legacyServiceGroup->status;
                $serviceGroupModel->undeletable = true;

                try {
                    if ($serviceGroup !== null) {
                        $this->serviceGroupRepository->update($serviceGroupModel);
                    } else {
                        $this->serviceGroupRepository->insert($serviceGroupModel);
                    }
                } catch (GenericException $e) {
                    $this->log->error('[Import] Failed to insert/update Service Group.', ['exception' => $e]);
                }
            }
        }

        return true;
    }

    private function getServiceGroupByKey(string $groupId): ?array
    {
        if (!Database::tableExists($this->wpdb->prefix . self::TABLE_NAME)) {
            return null;
        }

        $serviceGroups = $this->wpdb->get_results(
            $this->wpdb->prepare(
                '
                SELECT
                    `description`,
                    `group_id`,
                    `language`,
                    `name`,
                    `position`,
                    `pre_selected`,
                    `status`
                FROM
                    `' . $this->wpdb->prefix . self::TABLE_NAME . '`
                WHERE
                    `group_id` = %s
            ',
                [
                    $groupId,
                ],
            ),
        );

        return is_array($serviceGroups) ? $serviceGroups : null;
    }

    private function getServiceGroups(): ?array
    {
        if (!Database::tableExists($this->wpdb->prefix . self::TABLE_NAME)) {
            return null;
        }

        $serviceGroups = $this->wpdb->get_results('
            SELECT
                `description`,
                `group_id`,
                `language`,
                `name`,
                `position`,
                `pre_selected`,
                `status`
            FROM
                `' . $this->wpdb->prefix . self::TABLE_NAME . '`
        ');

        if (!is_array($serviceGroups) || count($serviceGroups) === 0) {
            return null;
        }

        return $serviceGroups;
    }
}
