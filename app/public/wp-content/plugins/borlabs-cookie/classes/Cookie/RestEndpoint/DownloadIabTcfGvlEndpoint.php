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
use Borlabs\Cookie\Dto\RestEndpoint\DownloadIabTcfGvlResponseDto;
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\Localization\IabTcf\IabTcfSettingsLocalizationStrings;
use Borlabs\Cookie\Support\Formatter;
use Borlabs\Cookie\System\IabTcf\IabTcfService;
use Borlabs\Cookie\System\Log\Log;

final class DownloadIabTcfGvlEndpoint implements RestEndpointInterface
{
    private IabTcfService $iabTcfService;

    private Log $log;

    private WpFunction $wpFunction;

    public function __construct(
        IabTcfService $iabTcfService,
        Log $log,
        WpFunction $wpFunction
    ) {
        $this->iabTcfService = $iabTcfService;
        $this->log = $log;
        $this->wpFunction = $wpFunction;
    }

    public function download(): DownloadIabTcfGvlResponseDto
    {
        $status = false;

        try {
            $status = $this->iabTcfService->updateGlobalVendorListFile();
            $this->iabTcfService->updatePurposeTranslationFiles();
            $this->iabTcfService->updateVendors();
            $message = IabTcfSettingsLocalizationStrings::get()['alert']['downloadGvlSuccessfully'];
        } catch (TranslatedException $e) {
            $message = $e->getTranslatedMessage();
            $this->log->error($message);
        }

        $lastSuccessfulCheckWithApiTimestamp = $this->iabTcfService->getLastSuccessfulCheckWithApiTimestamp();

        return new DownloadIabTcfGvlResponseDto(
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
            '/download/iab-tcf-gvl',
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
