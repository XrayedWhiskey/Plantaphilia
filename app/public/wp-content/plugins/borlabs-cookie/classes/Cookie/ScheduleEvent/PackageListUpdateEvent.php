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

namespace Borlabs\Cookie\ScheduleEvent;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\System\Package\PackageManager;

class PackageListUpdateEvent implements ScheduleEventInterface
{
    public const EVENT_NAME = 'PackageListUpdate';

    private PackageManager $packageManager;

    private WpFunction $wpFunction;

    public function __construct(
        PackageManager $packageManager,
        WpFunction $wpFunction
    ) {
        $this->packageManager = $packageManager;
        $this->wpFunction = $wpFunction;
    }

    public function deregister(): void
    {
        $this->wpFunction->wpClearScheduledHook(ScheduleEventManager::EVENT_PREFIX . self::EVENT_NAME);
    }

    public function register(): void
    {
        $this->wpFunction->addAction(ScheduleEventManager::EVENT_PREFIX . self::EVENT_NAME, [$this, 'run']);

        if (!$this->wpFunction->wpNextScheduled(ScheduleEventManager::EVENT_PREFIX . self::EVENT_NAME)) {
            $this->wpFunction->wpScheduleEvent(
                time(),
                'daily',
                ScheduleEventManager::EVENT_PREFIX . self::EVENT_NAME,
            );
        }
    }

    public function run(): void
    {
        try {
            $packageListLastUpdate = $this->packageManager->getLastSuccessfulCheckWithApiTimestamp();

            /*
             * Prevents the `updatePackageList` method from being executed
             * if the package list was successfully updated within the last 5 minutes.
             * This check avoids a race-condition issue that could occur during
             * the execution of the Legacy Importer.
             */
            if ($packageListLastUpdate > time() - 60 * 5) {
                return;
            }

            $this->packageManager->updatePackageList();
        } catch (GenericException $e) {
            // Log error
        }
    }
}
