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
use Borlabs\Cookie\Exception\ApiClient\LicenseApiClientException;
use Borlabs\Cookie\System\License\License;
use Borlabs\Cookie\System\Log\Log;

final class LicenseValidationEvent implements ScheduleEventInterface
{
    public const EVENT_NAME = 'LicenseValidation';

    private License $license;

    private Log $log;

    private WpFunction $wpFunction;

    public function __construct(
        License $license,
        Log $log,
        WpFunction $wpFunction
    ) {
        $this->license = $license;
        $this->log = $log;
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
            $this->license->validateLicense(true);
        } catch (LicenseApiClientException $e) {
            $this->log->error($e->getTranslatedMessage());
        }
    }
}
