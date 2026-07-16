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

namespace Borlabs\Cookie\System\Log;

use Borlabs\Cookie\Dto\Log\LogDto;
use Borlabs\Cookie\Enum\Log\LogLevelEnum;
use Borlabs\Cookie\Exception\GenericException;

class FileLog implements LogInterface
{
    public function addLogEntry(
        string $processId,
        LogLevelEnum $level,
        string $message,
        ?array $context = null,
        ?array $backtrace = null
    ): bool {
        $log = new LogDto($processId, $level, $message, $context, $backtrace);
        $this->writeToFile($log);

        return true;
    }

    /**
     * @throws \Borlabs\Cookie\Exception\GenericException
     */
    private function createProtectedDirectory()
    {
        $fileName = '.htaccess';
        $htaccessFileContent = 'AuthType Basic\nAuthName \"Restricted Access\"\nAuthUserFile .htnopasswd\nAuthGroupFile /dev/null\nRequire valid-user';
        $targetPath = WP_CONTENT_DIR . '/uploads/' . BORLABS_COOKIE_SLUG;
        $pathToProtectedDirctory = $targetPath . '/protected';

        if (!is_writable($targetPath)
            && (
                !file_exists($pathToProtectedDirctory)
                && !mkdir($pathToProtectedDirctory, 0777, true)
            )
        ) {
            throw new GenericException('couldNotCreateProtectedDirectory');
        }

        if (!file_exists($pathToProtectedDirctory . '/' . $fileName)) {
            if (!file_put_contents($pathToProtectedDirctory . '/' . $fileName, $htaccessFileContent, FILE_APPEND)) {
                throw new GenericException('couldNotProtectDirectory');
            }
        }
    }

    /**
     * @throws \Borlabs\Cookie\Exception\GenericException
     */
    private function writeToFile(LogDto $log)
    {
        $this->createProtectedDirectory();

        $fileName = 'borlabs-cookie.log';
        $path = WP_CONTENT_DIR . '/uploads/' . BORLABS_COOKIE_SLUG . '/protected';
        $fileContent = '[BORLABS_COOKIE][' . $log->processId . '][' . $log->level->key . '][' . $log->stamp->format('Y-m-d H:i:s') . '] '
            . $log->message . "\n"
            . "[CONTEXT]\n"
            . json_encode($log->context)
            . "\n[BACKTRACE]\n"
            . json_encode($log->backtrace)
            . "\n";

        if (!file_put_contents($path . '/' . $fileName, $fileContent, FILE_APPEND)) {
            throw new GenericException('couldNotWriteLogToFile');
        }
    }
}
