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
use Borlabs\Cookie\System\FileSystem\StorageFolder as SystemStorageFolder;

final class StorageFolder
{
    private FileSystem $fileSystem;

    private SystemStorageFolder $storageFolder;

    public function __construct(FileSystem $fileSystem, SystemStorageFolder $storageFolder)
    {
        $this->fileSystem = $fileSystem;
        $this->storageFolder = $storageFolder;
    }

    public function run(): AuditDto
    {
        return $this->createFolder();
    }

    private function createFolder(): AuditDto
    {
        return $this->fileSystem->createFolder(
            $this->storageFolder->getPath(),
            $this->storageFolder->getRootPath(),
            'uploads',
            'BORLABS_COOKIE_STORAGE_PATH',
        );
    }
}
