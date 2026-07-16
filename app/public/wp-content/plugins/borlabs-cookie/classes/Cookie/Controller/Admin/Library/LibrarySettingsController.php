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

namespace Borlabs\Cookie\Controller\Admin\Library;

use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\Enum\Library\AutoUpdateIntervalEnum;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\Localization\Library\LibrarySettingsLocalizationStrings;
use Borlabs\Cookie\System\Config\LibraryConfig;
use Borlabs\Cookie\System\Config\Traits\PackageAutoUpdateTimeHelperTrait;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\Template\Template;

final class LibrarySettingsController implements ControllerInterface
{
    use PackageAutoUpdateTimeHelperTrait;

    public const CONTROLLER_ID = 'borlabs-cookie-library-settings';

    private GlobalLocalizationStrings $globalLocalizationStrings;

    private LibraryConfig $libraryConfig;

    private MessageManager $messageManager;

    private Template $template;

    public function __construct(
        GlobalLocalizationStrings $globalLocalizationStrings,
        LibraryConfig $libraryConfig,
        MessageManager $messageManager,
        Template $template
    ) {
        $this->globalLocalizationStrings = $globalLocalizationStrings;
        $this->libraryConfig = $libraryConfig;
        $this->messageManager = $messageManager;
        $this->template = $template;
    }

    public function reset(): string
    {
        $this->libraryConfig->save(
            $this->libraryConfig->defaultConfig(),
        );

        $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['resetSuccessfully']);

        return $this->viewOverview();
    }

    public function route(RequestDto $request): ?string
    {
        $action = $request->postData['action'] ?? $request->getData['action'] ?? '';

        if ($action === 'reset') {
            return $this->reset();
        }

        if ($action === 'save') {
            return $this->save($request);
        }

        return $this->viewOverview();
    }

    public function save($request): string
    {
        $postData = $request->postData;
        $defaultPluginConfig = $this->libraryConfig->defaultConfig();
        $libraryConfig = $this->libraryConfig->get();
        $libraryConfig->packageAutoUpdateInterval = AutoUpdateIntervalEnum::hasValue($postData['packageAutoUpdateInterval'] ?? '') ? AutoUpdateIntervalEnum::fromValue($postData['packageAutoUpdateInterval']) : $defaultPluginConfig->packageAutoUpdateInterval;
        $libraryConfig->packageAutoUpdateTimeSpan = isset($postData['packageAutoUpdateTimeSpan']) && preg_match('/([0-1]{1}[0-9]{1}|2[0-3]):00-([0-1]{1}[0-9]{1}|2[0-3]):59/', $postData['packageAutoUpdateTimeSpan']) ? $postData['packageAutoUpdateTimeSpan'] : $defaultPluginConfig->packageAutoUpdateTimeSpan;
        $updateTime = $this->getRandomTimeWithinSpanIgnoringSeconds($libraryConfig->packageAutoUpdateTimeSpan);
        $libraryConfig->packageAutoUpdateTime = $updateTime->format('H:i');

        if (!empty($postData['packageAutoUpdateEmailAddresses'])) {
            $emailAddresses = [];
            $postData['packageAutoUpdateEmailAddresses'] = stripslashes($postData['packageAutoUpdateEmailAddresses']);
            $postData['packageAutoUpdateEmailAddresses'] = preg_split('/\r\n|[\r\n]/', $postData['packageAutoUpdateEmailAddresses']);

            if (!empty($postData['packageAutoUpdateEmailAddresses'])) {
                foreach ($postData['packageAutoUpdateEmailAddresses'] as $emailAddress) {
                    $emailAddress = trim(stripslashes($emailAddress));

                    if (!empty($emailAddress) && filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
                        $emailAddresses[$emailAddress] = $emailAddress;
                    }
                }
            }

            $libraryConfig->packageAutoUpdateEmailAddresses = array_values($emailAddresses);
        }

        if (count($libraryConfig->packageAutoUpdateEmailAddresses) === 0) {
            $libraryConfig->packageAutoUpdateEmailAddresses = $defaultPluginConfig->packageAutoUpdateEmailAddresses;
        }

        $libraryConfig->enableEmailNotificationsForUpdatablePackagesWithAutoUpdateDisabled = isset($postData['enableEmailNotificationsForUpdatablePackagesWithAutoUpdateDisabled']) && $postData['enableEmailNotificationsForUpdatablePackagesWithAutoUpdateDisabled'] === '1';
        $libraryConfig->enableEmailNotificationsForUpdatablePackagesWithAutoUpdateEnabled = isset($postData['enableEmailNotificationsForUpdatablePackagesWithAutoUpdateEnabled']) && $postData['enableEmailNotificationsForUpdatablePackagesWithAutoUpdateEnabled'] === '1';
        $this->libraryConfig->save($libraryConfig);
        $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['savedSuccessfully']);

        return $this->viewOverview();
    }

    public function viewOverview(): string
    {
        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = LibrarySettingsLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['data']['pluginConfig'] = $this->libraryConfig->get();
        $templateData['options']['packageAutoUpdateIntervals'] = AutoUpdateIntervalEnum::getLocalizedKeyValueList();
        $templateData['options']['packageAutoUpdateTimeSpan'] = $this->getTimeSpanList();

        return $this->template->getEngine()->render('library/library-settings/library-settings.html.twig', $templateData);
    }
}
