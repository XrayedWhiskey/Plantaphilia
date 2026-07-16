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

/**
 * @deprecated since 3.4.0 Use \Borlabs\Cookie\ScheduleEvent\PageSelectionKeywordListUpdateEvent instead.
 */
final class ScannerPageSelectionKeywordDatabaseUpdateEvent implements ScheduleEventInterface
{
    public const EVENT_NAME = 'ScannerPageSelectionKeywordDatabaseUpdate';

    private WpFunction $wpFunction;

    public function __construct(
        WpFunction $wpFunction
    ) {
        $this->wpFunction = $wpFunction;
    }

    public function deregister(): void
    {
        $this->wpFunction->wpClearScheduledHook(ScheduleEventManager::EVENT_PREFIX . self::EVENT_NAME);
    }

    public function register(): void
    {
    }

    public function run(): void
    {
        // Nothing
    }
}
