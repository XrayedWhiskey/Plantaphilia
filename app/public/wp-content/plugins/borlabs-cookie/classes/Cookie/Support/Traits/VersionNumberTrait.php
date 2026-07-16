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

namespace Borlabs\Cookie\Support\Traits;

use Borlabs\Cookie\Dto\System\VersionNumberDto;
use Borlabs\Cookie\Dto\System\VersionNumberWithHotfixDto;

trait VersionNumberTrait
{
    public function compareVersionNumber(VersionNumberDto $versionA, VersionNumberDto $versionB, string $operator)
    {
        $comparableVersionA = $this->transformVersionNumberToComparableString($versionA);
        $comparableVersionB = $this->transformVersionNumberToComparableString($versionB);

        return version_compare($comparableVersionA, $comparableVersionB, $operator);
    }

    public function transformToVersionNumberDto(string $version): VersionNumberDto
    {
        $matches = [];
        preg_match('/^(\d+)(\.(\d+))?(\.(\d+))?(\.(\d+))?$/', $version, $matches);

        return new VersionNumberDto(
            isset($matches[1]) ? (int) $matches[1] : 0,
            isset($matches[3]) ? (int) $matches[3] : 0,
            isset($matches[5]) ? (int) $matches[5] : 0,
        );
    }

    public function transformToVersionNumberWithHotfixDto(string $version): VersionNumberWithHotfixDto
    {
        $matches = [];
        preg_match('/^(\d+)(\.(\d+))?(\.(\d+))?(\.(\d+))?(\.(\d+))?$/', $version, $matches);

        return new VersionNumberWithHotfixDto(
            isset($matches[1]) ? (int) $matches[1] : 0,
            isset($matches[3]) ? (int) $matches[3] : 0,
            isset($matches[5]) ? (int) $matches[5] : 0,
            isset($matches[7]) ? (int) $matches[7] : 0,
        );
    }

    public function transformVersionNumberToComparableString(VersionNumberDto $versionNumberDto): string
    {
        if ($versionNumberDto instanceof VersionNumberWithHotfixDto) {
            return $versionNumberDto->major . '.' . $versionNumberDto->minor . '.' . $versionNumberDto->patch . '.' . $versionNumberDto->hotfix;
        }

        return $versionNumberDto->major . '.' . $versionNumberDto->minor . '.' . $versionNumberDto->patch . '.0';
    }

    public function versionNumberToString(VersionNumberDto $versionNumberDto): string
    {
        if ($versionNumberDto instanceof VersionNumberWithHotfixDto) {
            return $versionNumberDto->major . '.' . $versionNumberDto->minor . '.' . $versionNumberDto->patch . '.' . $versionNumberDto->hotfix;
        }

        return $versionNumberDto->major . '.' . $versionNumberDto->minor . '.' . $versionNumberDto->patch;
    }
}
