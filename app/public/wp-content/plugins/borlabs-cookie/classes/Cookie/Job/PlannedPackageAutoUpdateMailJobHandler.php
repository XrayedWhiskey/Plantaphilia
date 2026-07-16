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

namespace Borlabs\Cookie\Job;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Localization\Library\PlannedPackageAutoUpdateMailLocalizationStrings;
use Borlabs\Cookie\Localization\Library\SharedMailLocalizationStrings;
use Borlabs\Cookie\Model\Job\JobModel;
use Borlabs\Cookie\Repository\Package\PackageRepository;
use Borlabs\Cookie\Support\Formatter;
use Borlabs\Cookie\System\Config\LibraryConfig;
use Borlabs\Cookie\System\Localization\LocalizationForMailTemplates;
use Borlabs\Cookie\System\Mail\MailService;
use Borlabs\Cookie\System\Package\PackageAutoUpdateJobService;
use Borlabs\Cookie\System\Template\Template;

class PlannedPackageAutoUpdateMailJobHandler implements JobHandler
{
    public const JOB_TYPE = 'plannedPackageAutoUpdateMail';

    private LibraryConfig $libraryConfig;

    private LocalizationForMailTemplates $localizationForMailTemplates;

    private MailService $mailService;

    private PackageAutoUpdateJobService $packageAutoUpdateJobService;

    private PackageRepository $packageRepository;

    private Template $template;

    private WpFunction $wpFunction;

    public function __construct(
        LibraryConfig $libraryConfig,
        LocalizationForMailTemplates $localizationForMailTemplates,
        MailService $mailService,
        PackageAutoUpdateJobService $packageAutoUpdateJobService,
        PackageRepository $packageRepository,
        Template $template,
        WpFunction $wpFunction
    ) {
        $this->libraryConfig = $libraryConfig;
        $this->localizationForMailTemplates = $localizationForMailTemplates;
        $this->mailService = $mailService;
        $this->packageAutoUpdateJobService = $packageAutoUpdateJobService;
        $this->packageRepository = $packageRepository;
        $this->template = $template;
        $this->wpFunction = $wpFunction;
    }

    public function handle(JobModel $job): void
    {
        $plannedUpdates = $this->packageAutoUpdateJobService->getAllPlannedJobs();
        $updatablePackagesWithAutoUpdateDisabled = $this->packageRepository->getUpdatablePackages(false);
        $mailRecipients = $this->libraryConfig->get()->packageAutoUpdateEmailAddresses;

        $shouldSendEmail = false;

        if (count($plannedUpdates) && $this->libraryConfig->get()->enableEmailNotificationsForUpdatablePackagesWithAutoUpdateEnabled) {
            $shouldSendEmail = true;
        }

        if (count($updatablePackagesWithAutoUpdateDisabled) && $this->libraryConfig->get()->enableEmailNotificationsForUpdatablePackagesWithAutoUpdateDisabled) {
            $shouldSendEmail = true;
        }

        if (count($mailRecipients) === 0) {
            $shouldSendEmail = false;
        }

        if ($shouldSendEmail === false) {
            return;
        }

        $packageList = [];

        foreach ($plannedUpdates as $plannedUpdate) {
            $package = $this->packageRepository->getByPackageKey($plannedUpdate->payload['borlabsServicePackageKey']);

            if ($package !== null) {
                $packageList[] = [
                    'package' => $package,
                    'plannedFor' => $plannedUpdate->plannedFor,
                ];
            }
        }

        $templateData = $this->localizationForMailTemplates->replaceLocalizationTags(
            function () use (
                $packageList,
                $updatablePackagesWithAutoUpdateDisabled
            ) {
                $templateData = [];
                $templateData['localized'] = PlannedPackageAutoUpdateMailLocalizationStrings::get();
                $templateData['localized']['shared'] = SharedMailLocalizationStrings::get();
                $templateData['localized']['shared']['text']['mailInformation'] = Formatter::interpolate(
                    $templateData['localized']['shared']['text']['mailInformation'],
                    [
                        'websiteName' => $this->wpFunction->getBloginfo('name'),
                        'websiteUrl' => $this->wpFunction->getSiteUrl(),
                    ],
                );
                $templateData['data']['plannedPackageUpdates'] = $packageList;
                $templateData['data']['updatablePackagesWithAutoUpdateDisabled'] = $updatablePackagesWithAutoUpdateDisabled;

                return $templateData;
            },
        );

        $mailBody = $this->template->getEngine()->render(
            'mail/library/planned-package-auto-update.html.twig',
            $templateData,
        );

        foreach ($mailRecipients as $mailRecipient) {
            $this->mailService->sendMail(
                $mailRecipient,
                Formatter::interpolate(
                    $templateData['localized']['text']['subject'],
                    [
                        'numberOfAutomaticPackageUpdates' => count($plannedUpdates),
                        'numberOfManualPackageUpdates' => count($updatablePackagesWithAutoUpdateDisabled),
                    ],
                ),
                $mailBody,
            );
        }
    }
}
