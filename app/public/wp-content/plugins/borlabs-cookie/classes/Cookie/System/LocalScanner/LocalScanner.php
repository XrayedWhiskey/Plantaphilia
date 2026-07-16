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

namespace Borlabs\Cookie\System\LocalScanner;

class LocalScanner
{
    private ScannerInterface $scanner;

    private ScanRequestService $scanRequestService;

    private ScanResultService $scanResultService;

    private ScriptScanner $scriptScanner;

    private StyleScanner $styleScanner;

    public function __construct(
        ScanRequestService $scanRequestService,
        ScanResultService $scanResultService,
        ScriptScanner $scriptScanner,
        StyleScanner $styleScanner
    ) {
        $this->scanRequestService = $scanRequestService;
        $this->scanResultService = $scanResultService;
        $this->scriptScanner = $scriptScanner;
        $this->styleScanner = $styleScanner;
    }

    public function detectTags(): void
    {
        if (isset($this->scanner)) {
            $this->scanner->detectTags();
        }
    }

    public function init(): void
    {
        if ($this->scanRequestService->isScriptScanRequest()) {
            $this->scanner = $this->scriptScanner;
        } elseif ($this->scanRequestService->isStyleScanRequest()) {
            $this->scanner = $this->styleScanner;
        }

        if (isset($this->scanner)) {
            $this->scanner->init();
        }
    }

    public function saveScanResult(): void
    {
        if (isset($this->scanner)) {
            $this->scanResultService->saveScanResult();
        }
    }
}
