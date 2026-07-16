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

namespace Borlabs\Cookie\System\Installer\Service;

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Dto\System\AuditDto;
use Borlabs\Cookie\Model\ServiceGroup\ServiceGroupModel;
use Borlabs\Cookie\Repository\Service\ServiceCookieRepository;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\Repository\ServiceGroup\ServiceGroupRepository;

final class ServiceSeeder
{
    private ServiceCookieRepository $serviceCookieRepository;

    private ServiceDefaultEntries $serviceDefaultEntries;

    private ServiceGroupRepository $serviceGroupRepository;

    private ServiceRepository $serviceRepository;

    private WpDb $wpdb;

    public function __construct(
        ServiceCookieRepository $serviceCookieRepository,
        ServiceDefaultEntries $serviceDefaultEntries,
        ServiceGroupRepository $serviceGroupRepository,
        ServiceRepository $serviceRepository,
        WpDb $wpdb
    ) {
        $this->serviceCookieRepository = $serviceCookieRepository;
        $this->serviceDefaultEntries = $serviceDefaultEntries;
        $this->serviceGroupRepository = $serviceGroupRepository;
        $this->serviceRepository = $serviceRepository;
        $this->wpdb = $wpdb;
    }

    public function run(string $prefix, string $languageCode): AuditDto
    {
        // Sets the prefix to be used by the repository manager.
        $this->serviceRepository->overwriteTablePrefix($prefix);
        $this->serviceGroupRepository->overwriteTablePrefix($prefix);
        $this->serviceCookieRepository->overwriteTablePrefix($prefix);

        // Test "borlabs-cookie" exists and is active?
        $repositoryResult = $this->serviceRepository->find([
            'key' => 'borlabs-cookie',
            'language' => $languageCode,
        ]);

        if (!empty($repositoryResult[0]->id) && $repositoryResult[0]->status === true) {
            // Reset prefix
            $this->serviceRepository->overwriteTablePrefix();
            $this->serviceGroupRepository->overwriteTablePrefix();
            $this->serviceCookieRepository->overwriteTablePrefix();

            return new AuditDto(true);
        }

        // Service exists but is disabled. Try to re-enable it.
        if (!empty($repositoryResult[0]->id)) {
            $borlabsCookieModel = $repositoryResult[0];
            $borlabsCookieModel->status = true;
            $updateStatus = $this->serviceRepository->update($borlabsCookieModel);

            if ($updateStatus === false) {
                // Reset prefix
                $this->serviceRepository->overwriteTablePrefix();
                $this->serviceGroupRepository->overwriteTablePrefix();
                $this->serviceCookieRepository->overwriteTablePrefix();

                return new AuditDto(false, $this->wpdb->last_error);
            }
        }

        // Get 'Essential' service group id.
        /** @var ServiceGroupModel[] $serviceGroupRepositoryResult */
        $serviceGroupRepositoryResult = $this->serviceGroupRepository->find([
            'key' => 'essential',
            'language' => $languageCode,
        ]);

        if (empty($serviceGroupRepositoryResult[0]->id)) {
            // Reset prefix
            $this->serviceRepository->overwriteTablePrefix();
            $this->serviceGroupRepository->overwriteTablePrefix();
            $this->serviceCookieRepository->overwriteTablePrefix();

            return new AuditDto(false);
        }

        // Create default (essential) services
        $defaultServices = $this->serviceDefaultEntries->getDefaultEntries($languageCode);

        foreach ($defaultServices as $model) {
            // Test if service exists
            $result = $this->serviceRepository->find([
                'key' => $model->key,
                'language' => $languageCode,
            ]);

            if (!empty($result[0]->id)) {
                continue;
            }

            // Try to add
            $model->serviceGroupId = $serviceGroupRepositoryResult[0]->id;

            $newModel = $this->serviceRepository->insert($model);

            if (empty($newModel)) {
                // Reset prefix
                $this->serviceRepository->overwriteTablePrefix();
                $this->serviceGroupRepository->overwriteTablePrefix();
                $this->serviceCookieRepository->overwriteTablePrefix();

                return new AuditDto(false);
            }

            // Add service cookies
            if (isset($newModel->serviceCookies)) {
                foreach ($newModel->serviceCookies as $serviceCookie) {
                    $serviceCookie->serviceId = $newModel->id;
                    $this->serviceCookieRepository->insert($serviceCookie);
                }
            }
        }

        // Reset prefix
        $this->serviceRepository->overwriteTablePrefix();
        $this->serviceGroupRepository->overwriteTablePrefix();
        $this->serviceCookieRepository->overwriteTablePrefix();

        return new AuditDto(true);
    }
}
