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
use Borlabs\Cookie\System\FileSystem\CacheFolder as SystemCacheFolder;

final class CacheFolder
{
    private SystemCacheFolder $cacheFolder;

    private FileSystem $fileSystem;

    public function __construct(SystemCacheFolder $cacheFolder, FileSystem $fileSystem)
    {
        $this->cacheFolder = $cacheFolder;
        $this->fileSystem = $fileSystem;
    }

    public function run(): AuditDto
    {
        return $this->createFolder();
    }

    private function createFolder(): AuditDto
    {
        return $this->fileSystem->createFolder(
            $this->cacheFolder->getPath(),
            $this->cacheFolder->getRootPath(),
            'cache',
            'BORLABS_COOKIE_CACHE_PATH',
        );
    }
}
