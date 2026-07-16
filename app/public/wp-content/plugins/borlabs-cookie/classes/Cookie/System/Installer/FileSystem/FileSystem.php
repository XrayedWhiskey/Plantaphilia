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

namespace Borlabs\Cookie\System\Installer\FileSystem;

use Borlabs\Cookie\Dto\System\AuditDto;
use Borlabs\Cookie\Localization\SystemCheck\SystemCheckLocalizationStrings;
use Borlabs\Cookie\Support\Formatter;

final class FileSystem
{
    /**
     * @var string[][]
     */
    private ?array $localization = null;

    public function createFolder(
        string $targetPath,
        string $targetRootPath,
        string $targetRootName,
        string $constantName
    ): AuditDto {
        if ($this->localization === null) {
            $this->localization = SystemCheckLocalizationStrings::get();
        }

        // Custom folder defined
        if (defined($constantName)) {
            if (!file_exists($targetPath)) {
                return new AuditDto(
                    false,
                    Formatter::interpolate($this->localization['alert']['customFolderDoesNotExist'], [
                        'folder' => $targetPath,
                    ]),
                );
            }
        }

        // Our folder exists and is writable
        if (file_exists($targetPath) && is_writable($targetPath)) {
            return new AuditDto(true);
        }

        // Our folder exists but is not writable
        if (file_exists($targetPath) && !is_writable($targetPath)) {
            return new AuditDto(
                false,
                Formatter::interpolate($this->localization['alert']['folderIsNotWritable'], [
                    'folder' => $targetPath,
                ]),
            );
        }

        // Our folder doesn't exist but the global folder does and is writable
        if (file_exists($targetRootPath) && is_writable($targetRootPath)) {
            mkdir($targetPath, 0777, true);

            return new AuditDto(true);
        }

        // The global folder exists but is not writable
        if (file_exists($targetRootPath) && !is_writable($targetRootPath)) {
            return new AuditDto(
                false,
                Formatter::interpolate($this->localization['alert']['folderIsNotWritable'], [
                    'folder' => basename(WP_CONTENT_DIR) . '/' . $targetRootName,
                ]),
            );
        }

        // Global folder does not exist
        if (!is_writable(WP_CONTENT_DIR)) {
            return new AuditDto(
                false,
                Formatter::interpolate($this->localization['alert']['folderIsNotWritable'], [
                    'folder' => basename(WP_CONTENT_DIR) . '/' . $targetRootName,
                ]),
            );
        }

        mkdir(WP_CONTENT_DIR . '/' . $targetRootName);
        mkdir(WP_CONTENT_DIR . '/' . $targetRootName . '/' . BORLABS_COOKIE_SLUG);

        return new AuditDto(true);
    }
}
