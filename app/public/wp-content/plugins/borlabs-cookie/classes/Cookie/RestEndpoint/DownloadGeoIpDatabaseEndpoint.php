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

namespace Borlabs\Cookie\RestEndpoint;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Dto\RestEndpoint\DownloadGeoIpDatabaseResponseDto;
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\Localization\Dialog\DialogSettingsLocalizationStrings;
use Borlabs\Cookie\Support\Formatter;
use Borlabs\Cookie\System\GeoIp\GeoIp;
use Borlabs\Cookie\System\Log\Log;

final class DownloadGeoIpDatabaseEndpoint implements RestEndpointInterface
{
    private GeoIp $geoIp;

    private Log $log;

    private WpFunction $wpFunction;

    public function __construct(
        GeoIp $geoIp,
        Log $log,
        WpFunction $wpFunction
    ) {
        $this->geoIp = $geoIp;
        $this->log = $log;
        $this->wpFunction = $wpFunction;
    }

    public function download(): DownloadGeoIpDatabaseResponseDto
    {
        $status = false;

        try {
            $status = $this->geoIp->downloadGeoIpDatabase(true);
            $message = DialogSettingsLocalizationStrings::get()['alert']['downloadGeoIpDatabaseSuccessfully'];
        } catch (TranslatedException $e) {
            $message = $e->getTranslatedMessage();
            $this->log->error($message);
        }

        $lastSuccessfulCheckWithApiTimestamp = $this->geoIp->getLastSuccessfulCheckWithApiTimestamp();

        return new DownloadGeoIpDatabaseResponseDto(
            $status,
            strip_tags($message),
            $message,
            $lastSuccessfulCheckWithApiTimestamp === null
            ? null
            : Formatter::timestamp($lastSuccessfulCheckWithApiTimestamp),
        );
    }

    public function register(): void
    {
        $this->wpFunction->registerRestRoute(
            RestEndpointManager::NAMESPACE . '/v1',
            '/download/geo-ip-database',
            [
                'methods' => 'GET',
                'callback' => [$this, 'download'],
                'permission_callback' => function () {
                    return $this->wpFunction->currentUserCan('manage_borlabs_cookie');
                },
            ],
        );
    }
}
