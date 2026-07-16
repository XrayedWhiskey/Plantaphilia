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

namespace Borlabs\Cookie\System\Template;

use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\Localization\Layout\NavigationLocalizationStrings;
use Borlabs\Cookie\System\Config\PluginConfig;
use Borlabs\Cookie\System\Language\Language;

/**
 * Singleton class Layout.
 *
 * The **LayoutController** class returns the layout parts for header, navigation and footer.
 *
 * @see \Borlabs\Cookie\System\WordPressAdminDriver\ControllerManager::__call
 */
final class Layout
{
    private GlobalLocalizationStrings $globalLocalizationStrings;

    private Language $language;

    private PluginConfig $pluginConfig;

    private Template $template;

    public function __construct(
        GlobalLocalizationStrings $globalLocalizationStrings,
        Language $language,
        PluginConfig $pluginConfig,
        Template $template
    ) {
        $this->globalLocalizationStrings = $globalLocalizationStrings;
        $this->language = $language;
        $this->pluginConfig = $pluginConfig;
        $this->template = $template;
    }

    public function getFooter(): string
    {
        $templateData = [];
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();

        return $this->template->getEngine()->render(
            'layout/footer.html.twig',
            $templateData,
        );
    }

    public function getHeader(): string
    {
        $templateData = [];
        $templateData['displayModeSettings'] = $this->pluginConfig->get()->displayModeSettings;
        $templateData['language'] = $this->language->getSelectedLanguageCode();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();

        return $this->template->getEngine()->render('layout/header.html.twig', $templateData);
    }

    public function getNavigation(ControllerInterface $loadedController, RequestDto $request): string
    {
        $templateData = [];
        $templateData['activeModule'] = get_class($loadedController);
        $templateData['request'] = $request;
        $templateData['localized'] = NavigationLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();

        // Give info which language setting is loaded
        $templateData['data']['currentLanguageCode'] = $this->language->getSelectedLanguageCode();
        $templateData['data']['languageList'] = $this->language->getLanguageList();

        return $this->template->getEngine()->render(
            'layout/navigation.html.twig',
            $templateData,
        );
    }
}
