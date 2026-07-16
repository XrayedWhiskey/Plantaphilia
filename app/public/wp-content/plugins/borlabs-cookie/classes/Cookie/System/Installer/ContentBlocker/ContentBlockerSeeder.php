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

namespace Borlabs\Cookie\System\Installer\ContentBlocker;

use Borlabs\Cookie\Dto\System\AuditDto;
use Borlabs\Cookie\Repository\ContentBlocker\ContentBlockerRepository;

final class ContentBlockerSeeder
{
    private ContentBlockerDefaultEntries $contentBlockerDefaultEntries;

    private ContentBlockerRepository $contentBlockerRepository;

    public function __construct(
        ContentBlockerDefaultEntries $contentBlockerDefaultEntries,
        ContentBlockerRepository $contentBlockerRepository
    ) {
        $this->contentBlockerDefaultEntries = $contentBlockerDefaultEntries;
        $this->contentBlockerRepository = $contentBlockerRepository;
    }

    public function run(string $prefix, string $languageCode): AuditDto
    {
        // Sets the prefix to be used by the repository manager.
        $this->contentBlockerRepository->overwriteTablePrefix($prefix);

        $defaultContentBlocker = $this->contentBlockerDefaultEntries->getDefaultEntries($languageCode);

        foreach ($defaultContentBlocker as $model) {
            // Test if content blocker exists
            $result = $this->contentBlockerRepository->find([
                'key' => $model->key,
                'language' => $languageCode,
            ]);

            if (!empty($result[0]->id)) {
                continue;
            }

            // Try to add
            $newModel = $this->contentBlockerRepository->insert($model);

            if (empty($newModel)) {
                return new AuditDto(false);
            }
        }

        // Reset prefix
        $this->contentBlockerRepository->overwriteTablePrefix();

        return new AuditDto(true);
    }
}
