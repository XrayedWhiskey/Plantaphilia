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

use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\System\Log\Log;
use Borlabs\Cookie\System\ThirdPartyImporter\BorlabsCookieLegacy\BorlabsCookieLegacyImporter;

final class ThirdPartyImportManager
{
    private Container $container;

    private Log $log;

    private array $registry = [
        BorlabsCookieLegacyImporter::class,
    ];

    public function __construct(
        Container $container,
        Log $log
    ) {
        $this->container = $container;
        $this->log = $log;
    }

    /**
     * This method will be used in the future to display a list of available importers in the admin area.
     */
    public function getAvailableImporter(): KeyValueDtoList
    {
        $availableImporter = new KeyValueDtoList();

        foreach ($this->registry as $importer) {
            $importerInstance = $this->container->get($importer);

            if ($importerInstance instanceof ThirdPartyImporterInterface && $importerInstance->isImportDataAvailable()) {
                $availableImporter->add(
                    new KeyValueDto(
                        $importer,
                        $importerInstance->getImporterName(),
                    ),
                );
            }
        }

        return $availableImporter;
    }

    public function import()
    {
        /*
         * This loop is designed to import data from all available importers.
         * However, in practice, we currently have only one importer, and customers typically will not have
         * multiple CMPs (Consent Management Platforms) to import data from.
         */
        foreach ($this->registry as $importer) {
            $importerInstance = $this->container->get($importer);

            if ($importerInstance instanceof ThirdPartyImporterInterface) {
                if ($importerInstance->isImportDataAvailable() && $importerInstance->shouldImport()) {
                    $this->log->info(
                        '[Import] Import data available for: {{ importerName }}',
                        [
                            'importerName' => $importerInstance->getImporterName(),
                        ],
                    );
                    $importerInstance->import();
                }
            }
        }
    }
}
