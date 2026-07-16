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

namespace Borlabs\Cookie\Controller\Admin\ContentBlocker;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\Localization\ContentBlocker\ContentBlockerSettingsLocalizationStrings;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\Repository\ContentBlocker\ContentBlockerRepository;
use Borlabs\Cookie\Support\Sanitizer;
use Borlabs\Cookie\System\Config\ContentBlockerSettingsConfig;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\Template\Template;
use Borlabs\Cookie\System\ThirdPartyCacheClearer\ThirdPartyCacheClearerManager;

final class ContentBlockerSettingsController implements ControllerInterface
{
    public const CONTROLLER_ID = 'borlabs-cookie-content-blocker-settings';

    private ContentBlockerRepository $contentBlockerRepository;

    private ContentBlockerSettingsConfig $contentBlockerSettingsConfig;

    private GlobalLocalizationStrings $globalLocalizationStrings;

    private Language $language;

    private MessageManager $messageManager;

    private Template $template;

    private ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager;

    private WpFunction $wpFunction;

    public function __construct(
        ContentBlockerRepository $contentBlockerRepository,
        ContentBlockerSettingsConfig $contentBlockerSettingsConfig,
        GlobalLocalizationStrings $globalLocalizationStrings,
        Language $language,
        MessageManager $messageManager,
        Template $template,
        ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager,
        WpFunction $wpFunction
    ) {
        $this->contentBlockerRepository = $contentBlockerRepository;
        $this->contentBlockerSettingsConfig = $contentBlockerSettingsConfig;
        $this->globalLocalizationStrings = $globalLocalizationStrings;
        $this->language = $language;
        $this->messageManager = $messageManager;
        $this->template = $template;
        $this->thirdPartyCacheClearerManager = $thirdPartyCacheClearerManager;
        $this->wpFunction = $wpFunction;
    }

    public function reset(): string
    {
        $this->contentBlockerSettingsConfig->save(
            $this->contentBlockerSettingsConfig->defaultConfig(),
            $this->language->getSelectedLanguageCode(),
        );
        $this->thirdPartyCacheClearerManager->clearCache();

        $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['resetSuccessfully']);

        return $this->viewOverview();
    }

    /**
     * Is loaded by {@see \Borlabs\Cookie\System\WordPressAdminDriver\ControllerManager::load()} and gets information
     * what about to do.
     *
     * @throws \Borlabs\Cookie\Dependencies\Twig\Error\Error
     */
    public function route(RequestDto $request): ?string
    {
        $action = $request->postData['action'] ?? $request->getData['action'] ?? '';

        if ($action === 'reset') {
            return $this->reset();
        }

        if ($action === 'save') {
            return $this->save($request->postData);
        }

        return $this->viewOverview();
    }

    public function save(array $postData): string
    {
        $contentBlockerSettingsConfig = $this->contentBlockerSettingsConfig->get();
        $contentBlockerSettingsConfig->excludedHostnames = Sanitizer::hostList($postData['excludedHostnames'] ?? []);
        $contentBlockerSettingsConfig->removeIframesInFeeds = (bool) ($postData['removeIframesInFeeds'] ?? false);

        // Save config for other languages
        $languages = array_keys(
            array_filter(
                $postData['languages']['configuration'] ?? [],
                fn ($checked) => $checked === '1',
            ),
        );

        foreach ($languages as $languageCode) {
            $this->contentBlockerSettingsConfig->save($contentBlockerSettingsConfig, $languageCode);
        }

        // Save config for this language. The save routine also updates the current language object.
        $this->contentBlockerSettingsConfig->save(
            $contentBlockerSettingsConfig,
            $this->language->getSelectedLanguageCode(),
        );
        $this->thirdPartyCacheClearerManager->clearCache();

        $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['savedSuccessfully']);

        return $this->viewOverview();
    }

    /**
     * Returns the overview.
     *
     * @throws \Borlabs\Cookie\Dependencies\Twig\Error\Error
     */
    public function viewOverview(): string
    {
        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = ContentBlockerSettingsLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['data'] = (array) $this->contentBlockerSettingsConfig->get();
        $templateData['languages'] = $this->language->getLanguageList();
        $disabledContentBlockers = $this->contentBlockerRepository->getAllOfSelectedLanguage(true, false);
        $disabledContentBlockersHostnamesLists = array_filter(
            array_column(
                $disabledContentBlockers,
                'contentBlockerLocations',
            ),
            fn ($contentBlockerLocations) => count($contentBlockerLocations),
        );

        $disabledContentBlockersHostnamesList = [];
        array_walk(
            $disabledContentBlockersHostnamesLists,
            function ($list) use (&$disabledContentBlockersHostnamesList) {
                $disabledContentBlockersHostnamesList = array_merge($disabledContentBlockersHostnamesList, $list);
            },
        );
        $disabledContentBlockersHostnamesList = array_column($disabledContentBlockersHostnamesList, 'hostname');
        $disabledContentBlockersHostnamesList = $this->wpFunction->applyFilter(
            'borlabsCookie/contentBlocker/modifyExcludedHostnames',
            $disabledContentBlockersHostnamesList,
        );
        $templateData['data']['disabledContentBlockersHostnames'] = is_array($disabledContentBlockersHostnamesList)
            ? Sanitizer::hostArray($disabledContentBlockersHostnamesList)
            : [];

        return $this->template->getEngine()->render(
            'content-blocker/content-blocker-settings/content-blocker-settings.html.twig',
            $templateData,
        );
    }
}
