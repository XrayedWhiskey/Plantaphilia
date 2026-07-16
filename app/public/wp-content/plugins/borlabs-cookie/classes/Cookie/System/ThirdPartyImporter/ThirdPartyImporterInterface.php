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

namespace Borlabs\Cookie\System\ThirdPartyImporter;

use Borlabs\Cookie\Dto\ThirdPartyImporter\ImportReportDto;

interface ThirdPartyImporterInterface
{
    // Get the importer name.
    public function getImporterName(): string;

    // Perform import
    public function import(): ?ImportReportDto;

    // Check if the import is completed.
    public function isImportCompleted(): bool;

    // Check if import data is available for the importer.
    public function isImportDataAvailable(): bool;

    // Check if the importer should import data.
    public function shouldImport(): bool;
}
