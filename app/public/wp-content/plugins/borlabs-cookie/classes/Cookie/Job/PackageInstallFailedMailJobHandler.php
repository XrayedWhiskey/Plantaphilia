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
use Borlabs\Cookie\Localization\Library\PackageInstallFailedMailLocalizationStrings;
use Borlabs\Cookie\Localization\Library\SharedMailLocalizationStrings;
use Borlabs\Cookie\Model\Job\JobModel;
use Borlabs\Cookie\Repository\Package\PackageRepository;
use Borlabs\Cookie\Support\Formatter;
use Borlabs\Cookie\System\Config\LibraryConfig;
use Borlabs\Cookie\System\Localization\LocalizationForMailTemplates;
use Borlabs\Cookie\System\Mail\MailService;
use Borlabs\Cookie\System\Template\Template;

class PackageInstallFailedMailJobHandler implements JobHandler
{
    public const JOB_TYPE = 'packageInstallFailedMail';

    private LibraryConfig $libraryConfig;

    private LocalizationForMailTemplates $localizationForMailTemplates;

    private MailService $mailService;

    private PackageRepository $packageRepository;

    private Template $template;

    private WpFunction $wpFunction;

    public function __construct(
        LibraryConfig $libraryConfig,
        LocalizationForMailTemplates $localizationForMailTemplates,
        MailService $mailService,
        PackageRepository $packageRepository,
        Template $template,
        WpFunction $wpFunction
    ) {
        $this->libraryConfig = $libraryConfig;
        $this->localizationForMailTemplates = $localizationForMailTemplates;
        $this->mailService = $mailService;
        $this->packageRepository = $packageRepository;
        $this->template = $template;
        $this->wpFunction = $wpFunction;
    }

    public function handle(JobModel $job): void
    {
        $package = $this->packageRepository->getByPackageKey($job->payload['borlabsServicePackageKey']);

        if ($package === null) {
            return;
        }

        $mailRecipients = $this->libraryConfig->get()->packageAutoUpdateEmailAddresses;
        $templateData = $this->localizationForMailTemplates->replaceLocalizationTags(
            function () use (
                $package,
                $job
            ) {
                $templateData = [];
                $templateData['localized'] = PackageInstallFailedMailLocalizationStrings::get();
                $templateData['localized']['shared'] = SharedMailLocalizationStrings::get();
                $templateData['localized']['shared']['text']['mailInformation'] = Formatter::interpolate(
                    $templateData['localized']['shared']['text']['mailInformation'],
                    [
                        'websiteName' => $this->wpFunction->getBloginfo('name'),
                        'websiteUrl' => $this->wpFunction->getSiteUrl(),
                    ],
                );
                $templateData['localized']['text']['automaticInstallationFailedA'] = Formatter::interpolate(
                    $templateData['localized']['text']['automaticInstallationFailedA'],
                    [
                        'packageName' => $package->name,
                    ],
                );
                $templateData['localized']['text']['automaticInstallationFailedB'] = Formatter::interpolate(
                    $templateData['localized']['text']['automaticInstallationFailedB'],
                    [
                        'processId' => $job->payload['processId'],
                    ],
                );
                $templateData['data']['job'] = $job;
                $templateData['data']['package'] = $package;

                return $templateData;
            },
        );

        $mailBody = $this->template->getEngine()->render(
            'mail/library/package-install-failed.html.twig',
            $templateData,
        );

        foreach ($mailRecipients as $mailRecipient) {
            $this->mailService->sendMail(
                $mailRecipient,
                Formatter::interpolate(
                    $templateData['localized']['text']['subject'],
                    [
                        'packageName' => $package->name,
                    ],
                ),
                $mailBody,
            );
        }
    }
}
