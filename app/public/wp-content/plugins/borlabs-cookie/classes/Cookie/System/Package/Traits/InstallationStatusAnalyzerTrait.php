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

namespace Borlabs\Cookie\System\Package\Traits;

use Borlabs\Cookie\DtoList\Package\InstallationStatusDtoList;
use Borlabs\Cookie\Enum\Package\InstallationStatusEnum;

trait InstallationStatusAnalyzerTrait
{
    /**
     * @return string[]
     */
    private function getErrorMessages(InstallationStatusDtoList $installationStatusDtoList): array
    {
        $errorMessages = [];

        foreach ($installationStatusDtoList->list as $installationStatusDto) {
            if ($installationStatusDto->status->is(InstallationStatusEnum::FAILURE())) {
                $errorMessages[] = $installationStatusDto->failureMessage;
            }

            if (isset($installationStatusDto->subComponentsInstallationStatus)) {
                $errorMessages = array_merge($errorMessages, $this->getErrorMessages($installationStatusDto->subComponentsInstallationStatus));
            }
        }

        return $errorMessages;
    }

    private function isInstallationCompletelySuccessful(InstallationStatusDtoList $installationStatusDtoList): bool
    {
        foreach ($installationStatusDtoList->list as $installationStatusDto) {
            if (!$installationStatusDto->status->is(InstallationStatusEnum::SUCCESS())) {
                return false;
            }

            if (isset($installationStatusDto->subComponentsInstallationStatus)) {
                if (!$this->isInstallationCompletelySuccessful($installationStatusDto->subComponentsInstallationStatus)) {
                    return false;
                }
            }
        }

        return true;
    }
}
