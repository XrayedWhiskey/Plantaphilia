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

use Borlabs\Cookie\Container\Container;

final class RestEndpointManager
{
    public const NAMESPACE = 'borlabs-cookie';

    private Container $container;

    private array $registry = [
        AuthorizationTestEndpoint::class,
        CloudScanEndpoint::class,
        CloudScanResultEndpoint::class,
        CloudScanSuggestionEndpoint::class,
        ConsentLogEndpoint::class,
        ConsentHistoryEndpoint::class,
        ConsentStatisticEndpoint::class,
        DialogVisibilityEndpoint::class,
        DownloadGeoIpDatabaseEndpoint::class,
        DownloadIabTcfGvlEndpoint::class,
        DownloadPageSelectionKeywordListEndpoint::class,
        LegalPagesDetectionEndpoint::class,
        PackageEndpoint::class,
        PackagePostInstallEndpoint::class,
        ScanEndpoint::class,
        ScanRequestEndpoint::class,
        ScanResultEndpoint::class,
        SetupAssistantFinalizeEndpoint::class,
        SetupAssistantGenerateFilesEndpoint::class,
        TestEndpoint::class,
    ];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function register(): void
    {
        foreach ($this->registry as $endpointClass) {
            /** @var RestEndpointInterface $endpoint */
            $endpoint = $this->container->get($endpointClass);
            $endpoint->register();
        }
    }
}
