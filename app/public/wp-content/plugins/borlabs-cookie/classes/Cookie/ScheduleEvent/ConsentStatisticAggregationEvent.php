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
use Borlabs\Cookie\System\ConsentStatistic\ConsentStatisticService;
use Exception;

final class ConsentStatisticAggregationEvent implements ScheduleEventInterface
{
    public const EVENT_NAME = 'ConsentStatisticAggregation';

    private ConsentStatisticService $consentStatisticService;

    private WpFunction $wpFunction;

    public function __construct(
        ConsentStatisticService $consentStatisticService,
        WpFunction $wpFunction
    ) {
        $this->consentStatisticService = $consentStatisticService;
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
                'hourly',
                ScheduleEventManager::EVENT_PREFIX . self::EVENT_NAME,
            );
        }
    }

    /**
     * @throws Exception
     */
    public function run(): void
    {
        $this->consentStatisticService->aggregateHourEntries();
    }
}
