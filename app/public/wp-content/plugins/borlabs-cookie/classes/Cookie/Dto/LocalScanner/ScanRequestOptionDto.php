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

namespace Borlabs\Cookie\Dto\LocalScanner;

use Borlabs\Cookie\Dto\AbstractDto;

class ScanRequestOptionDto extends AbstractDto
{
    public bool $noBorlabsCookie = false;

    public bool $noCompatibilityPatches = false;

    public bool $noConsentDialog = false;

    public bool $noContentBlockers = false;

    public bool $noDefaultContentBlocker = false;

    public bool $noScriptBlockers = false;

    public bool $noStyleBlockers = false;

    public bool $scriptScanRequest = false;

    public bool $styleScanRequest = false;

    public bool $withDebugConsole = false;
}
