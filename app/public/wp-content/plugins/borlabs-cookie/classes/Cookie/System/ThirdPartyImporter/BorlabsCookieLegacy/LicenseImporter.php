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

namespace Borlabs\Cookie\System\ThirdPartyImporter\BorlabsCookieLegacy;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Exception\ApiClient\LicenseApiClientException;
use Borlabs\Cookie\Exception\HttpClient\ConnectionErrorException;
use Borlabs\Cookie\Exception\HttpClient\ServerErrorException;
use Borlabs\Cookie\System\License\License;
use Borlabs\Cookie\System\Log\Log;
use Borlabs\Cookie\System\Option\Option;

final class LicenseImporter
{
    private License $license;

    private Log $log;

    private Option $option;

    private WpFunction $wpFunction;

    public function __construct(
        License $license,
        Log $log,
        Option $option,
        WpFunction $wpFunction
    ) {
        $this->license = $license;
        $this->log = $log;
        $this->option = $option;
        $this->wpFunction = $wpFunction;
    }

    /**
     * Retrieves the license key from the legacy plugin and attempts to register the license again to obtain the latest license data.
     *
     * @throws \Borlabs\Cookie\Exception\ApiClient\LicenseApiClientException
     */
    public function import(): bool
    {
        // Reload license data to ensure the correct license is applied during the migration process in a multisite environment.
        $this->license->get(true);

        if ($this->license->isPluginUnlocked()) {
            $this->log->info('[Import] License imported skipped, plugin is already unlocked.');

            return true;
        }

        $licenseKey = $this->wpFunction->isMultisite() ? $this->option->getGlobal('LegacyLicenseKey', null)->value : $this->option->get('LegacyLicenseKey', null)->value;

        if ($licenseKey === null) {
            $this->log->error(
                '[Import] No legacy license key found.',
            );

            return false;
        }

        $licenseRegisterStatus = false;

        try {
            $licenseRegisterStatus = $this->license->register($licenseKey);
        } catch (LicenseApiClientException $e) {
        } catch (ConnectionErrorException $e) {
        } catch (ServerErrorException $e) {
        }

        $this->log->info(
            '[Import] License imported: {{ status }}',
            [
                'status' => $licenseRegisterStatus ? 'Yes' : 'No',
            ],
        );

        return $licenseRegisterStatus;
    }
}
