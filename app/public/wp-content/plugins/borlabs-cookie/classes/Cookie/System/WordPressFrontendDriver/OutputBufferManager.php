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

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\System\LocalScanner\ScanRequestService;
use Borlabs\Cookie\System\ScriptBlocker\ScriptBlockerManager;
use Borlabs\Cookie\System\StyleBlocker\StyleBlockerManager;

final class OutputBufferManager
{
    private $buffer = '';

    private bool $isActive = false;

    private ScanRequestService $scanRequestService;

    private ScriptBlockerManager $scriptBlockerManager;

    private StyleBlockerManager $styleBlockerManager;

    private bool $useWordPressOutputBuffer = false;

    private WpFunction $wpFunction;

    public function __construct(
        ScanRequestService $scanRequestService,
        ScriptBlockerManager $scriptBlockerManager,
        StyleBlockerManager $styleBlockerManager,
        WpFunction $wpFunction
    ) {
        $this->scanRequestService = $scanRequestService;
        $this->scriptBlockerManager = $scriptBlockerManager;
        $this->styleBlockerManager = $styleBlockerManager;
        $this->wpFunction = $wpFunction;
    }

    public function &getBuffer(): string
    {
        return $this->buffer;
    }

    public function clearBuffer(): void
    {
        $this->buffer = '';
    }

    public function endBuffering(): bool
    {
        if ($this->isActive === true) {
            if (!$this->useWordPressOutputBuffer) {
                $this->buffer = ob_get_contents();
                ob_end_clean();
            }

            $this->isActive = false;

            return true;
        }

        return false;
    }

    public function isBufferingActive(): bool
    {
        return $this->isActive;
    }

    /**
     * Returns whether WordPress's output buffer system is active.
     *
     * Note: Only available with WordPress 6.9+.
     *
     * @since 3.3.21 First time this was introduced in Borlabs Cookie.
     */
    public function isWordPressOutputBufferActive(): bool
    {
        return $this->useWordPressOutputBuffer;
    }

    /**
     * Output the buffer via echo and clear the buffer.
     */
    public function outputBuffer(): void
    {
        if (!$this->useWordPressOutputBuffer) {
            echo $this->buffer;
        }

        $this->clearBuffer();
    }

    public function setBuffer(string $buffer): void
    {
        $this->buffer = $buffer;
    }

    /**
     * Enables using WordPress's output buffer system.
     *
     * Note: Only available with WordPress 6.9+.
     *
     * @since 3.3.21 First time this was introduced in Borlabs Cookie.
     */
    public function setWordPressOutputBufferStarted(): void
    {
        $this->useWordPressOutputBuffer = true;
    }

    public function startBuffering(): bool
    {
        if (
            $this->scanRequestService->isScanRequest() === false
            && $this->scriptBlockerManager->hasScriptBlockers() === false
            && $this->styleBlockerManager->hasStyleBlockers() === false
        ) {
            return false;
        }

        // Allow to disable the buffering when a Page Builder is active
        $this->isActive = $this->wpFunction->applyFilter('borlabsCookie/outputBufferManager/status', true);

        if ($this->isActive) {
            if (!$this->useWordPressOutputBuffer) {
                ob_start();
            }

            return true;
        }

        return false;
    }
}
