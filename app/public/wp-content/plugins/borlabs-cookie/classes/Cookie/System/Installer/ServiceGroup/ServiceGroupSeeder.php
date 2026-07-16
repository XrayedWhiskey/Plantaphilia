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

namespace Borlabs\Cookie\System\Installer\ServiceGroup;

use Borlabs\Cookie\Dto\System\AuditDto;
use Borlabs\Cookie\Repository\ServiceGroup\ServiceGroupRepository;

final class ServiceGroupSeeder
{
    private ServiceGroupDefaultEntries $serviceGroupDefaultEntries;

    private ServiceGroupRepository $serviceGroupRepository;

    public function __construct(
        ServiceGroupDefaultEntries $serviceGroupDefaultEntries,
        ServiceGroupRepository $serviceGroupRepository
    ) {
        $this->serviceGroupDefaultEntries = $serviceGroupDefaultEntries;
        $this->serviceGroupRepository = $serviceGroupRepository;
    }

    public function run(string $prefix, string $languageCode): AuditDto
    {
        // Sets the prefix to be used by the repository manager.
        $this->serviceGroupRepository->overwriteTablePrefix($prefix);
        $defaultServiceGroups = $this->serviceGroupDefaultEntries->getDefaultEntries($languageCode);

        foreach ($defaultServiceGroups as $model) {
            // Test if service group exists
            $result = $this->serviceGroupRepository->find([
                'key' => $model->key,
                'language' => $languageCode,
            ]);

            if (!empty($result[0]->id)) {
                continue;
            }

            // Try to add
            $newModel = $this->serviceGroupRepository->insert($model);

            if (empty($newModel)) {
                // Reset prefix
                $this->serviceGroupRepository->overwriteTablePrefix();

                return new AuditDto(false);
            }
        }

        // Reset prefix
        $this->serviceGroupRepository->overwriteTablePrefix();

        return new AuditDto(true);
    }
}
