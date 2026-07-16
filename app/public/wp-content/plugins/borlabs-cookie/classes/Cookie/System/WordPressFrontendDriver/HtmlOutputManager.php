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

namespace Borlabs\Cookie\System\WordPressFrontendDriver;

use Borlabs\Cookie\System\LocalScanner\LocalScanner;
use Borlabs\Cookie\System\ScriptBlocker\ScriptBlockerManager;
use Borlabs\Cookie\System\StyleBlocker\StyleBlockerManager;

final class HtmlOutputManager
{
    private LocalScanner $localScanner;

    private OutputBufferManager $outputBufferManager;

    private ScriptBlockerManager $scriptBlockerManager;

    private StyleBlockerManager $styleBlockerManager;

    public function __construct(
        LocalScanner $localScanner,
        OutputBufferManager $outputBufferManager,
        ScriptBlockerManager $scriptBlockerManager,
        StyleBlockerManager $styleBlockerManager
    ) {
        $this->localScanner = $localScanner;
        $this->outputBufferManager = $outputBufferManager;
        $this->scriptBlockerManager = $scriptBlockerManager;
        $this->styleBlockerManager = $styleBlockerManager;
    }

    public function handleWithNativeBuffer()
    {
        if (!$this->outputBufferManager->isBufferingActive()) {
            return;
        }

        $this->outputBufferManager->endBuffering();
        $this->localScanner->detectTags();
        $this->localScanner->saveScanResult();
        $this->scriptBlockerManager->blockUnregisteredScriptTags();
        $this->styleBlockerManager->blockUnregisteredStyleTags();
        $this->outputBufferManager->outputBuffer();
    }

    public function handleWithWordPressBuffer(string $buffer): string
    {
        if (!$this->outputBufferManager->isBufferingActive()) {
            return $buffer;
        }

        $this->outputBufferManager->setBuffer($buffer);
        $this->outputBufferManager->endBuffering();
        $this->localScanner->detectTags();
        $this->localScanner->saveScanResult();
        $this->scriptBlockerManager->blockUnregisteredScriptTags();
        $this->styleBlockerManager->blockUnregisteredStyleTags();
        $buffer = $this->outputBufferManager->getBuffer();
        $this->outputBufferManager->clearBuffer();

        return $buffer;
    }
}
