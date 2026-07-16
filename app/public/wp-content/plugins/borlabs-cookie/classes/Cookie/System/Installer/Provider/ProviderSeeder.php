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

namespace Borlabs\Cookie\System\Installer\Provider;

use Borlabs\Cookie\Dto\System\AuditDto;
use Borlabs\Cookie\Repository\Provider\ProviderRepository;

final class ProviderSeeder
{
    private ProviderDefaultEntries $providerDefaultEntries;

    private ProviderRepository $providerRepository;

    public function __construct(
        ProviderDefaultEntries $providerDefaultEntries,
        ProviderRepository $providerRepository
    ) {
        $this->providerDefaultEntries = $providerDefaultEntries;
        $this->providerRepository = $providerRepository;
    }

    public function run(string $prefix, string $languageCode): AuditDto
    {
        // Sets the prefix to be used by the repository manager.
        $this->providerRepository->overwriteTablePrefix($prefix);
        $defaultProviders = $this->providerDefaultEntries->getDefaultEntries($languageCode);

        foreach ($defaultProviders as $model) {
            // Test if provider exists
            $result = $this->providerRepository->find([
                'borlabsServiceProviderKey' => $model->borlabsServiceProviderKey,
                'language' => $languageCode,
            ]);

            if (!empty($result[0]->id)) {
                continue;
            }

            // Try to add
            $newModel = $this->providerRepository->insert($model);

            if (empty($newModel)) {
                // Reset prefix
                $this->providerRepository->overwriteTablePrefix();

                return new AuditDto(false);
            }
        }

        // Reset prefix
        $this->providerRepository->overwriteTablePrefix();

        return new AuditDto(true);
    }
}
