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

namespace Borlabs\Cookie\Controller\Admin\Widget;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\Localization\Widget\WidgetLocalizationStrings;
use Borlabs\Cookie\System\Config\IabTcfConfig;
use Borlabs\Cookie\System\Config\WidgetConfig;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\Script\ScriptConfigBuilder;
use Borlabs\Cookie\System\Style\StyleBuilder;
use Borlabs\Cookie\System\Template\Template;
use Borlabs\Cookie\System\ThirdPartyCacheClearer\ThirdPartyCacheClearerManager;

/**
 * The **WidgetController** class takes care of displaying the Widget section in the backend.
 * It also processes all requests that can be executed in the Widget section.
 */
final class WidgetController implements ControllerInterface
{
    public const CONTROLLER_ID = 'borlabs-cookie-widget';

    private GlobalLocalizationStrings $globalLocalizationStrings;

    private IabTcfConfig $iabTcfConfig;

    private Language $language;

    private MessageManager $messageManager;

    private ScriptConfigBuilder $scriptConfigBuilder;

    private StyleBuilder $styleBuilder;

    private Template $template;

    private ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager;

    private WidgetConfig $widgetConfig;

    private WpFunction $wpFunction;

    public function __construct(
        GlobalLocalizationStrings $globalLocalizationStrings,
        IabTcfConfig $iabTcfConfig,
        Language $language,
        MessageManager $messageManager,
        ScriptConfigBuilder $scriptConfigBuilder,
        StyleBuilder $styleBuilder,
        Template $template,
        ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager,
        WidgetConfig $widgetConfig,
        WpFunction $wpFunction
    ) {
        $this->globalLocalizationStrings = $globalLocalizationStrings;
        $this->iabTcfConfig = $iabTcfConfig;
        $this->language = $language;
        $this->messageManager = $messageManager;
        $this->scriptConfigBuilder = $scriptConfigBuilder;
        $this->styleBuilder = $styleBuilder;
        $this->template = $template;
        $this->thirdPartyCacheClearerManager = $thirdPartyCacheClearerManager;
        $this->widgetConfig = $widgetConfig;
        $this->wpFunction = $wpFunction;
    }

    /**
     * Resets the widget configuration to the default state.
     *
     * @return bool true on success
     */
    public function reset(): bool
    {
        // Save config
        $this->widgetConfig->save($this->widgetConfig->defaultConfig(), $this->language->getSelectedLanguageCode());
        $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion(
            $this->language->getSelectedLanguageCode(),
        );
        $this->styleBuilder->updateCssFileAndIncrementStyleVersion(
            $this->wpFunction->getCurrentBlogId(),
            $this->language->getSelectedLanguageCode(),
        );
        $this->thirdPartyCacheClearerManager->clearCache();

        $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['resetSuccessfully']);

        return true;
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
            $this->reset();
        } elseif ($action === 'save') {
            $this->save($request->postData);
        }

        return $this->viewOverview();
    }

    /**
     * Updates the configuration.
     *
     * @param array<string> $postData
     */
    public function save(array $postData): bool
    {
        $iabTcfConfig = $this->iabTcfConfig->get();
        $widgetConfig = $this->widgetConfig->get();
        $widgetConfig->color = (string) $postData['color'];
        $widgetConfig->icon = 'borlabs-cookie-widget-a.svg';

        if (in_array($postData['icon'], ['borlabs-cookie-widget-b.svg', 'borlabs-cookie-widget-c.svg'], true)) {
            $widgetConfig->icon = $postData['icon'];
        }

        $widgetConfig->position = (string) $postData['position'];
        // When utilizing the IAB TCF, enabling the widget is mandatory as it's part of the requirements.
        $widgetConfig->show = (bool) ($iabTcfConfig->iabTcfStatus ?: $postData['show']);

        // Save config for other languages
        $languages = array_keys(
            array_filter(
                $postData['languages']['configuration'] ?? [],
                fn ($checked) => $checked === '1',
            ),
        );

        foreach ($languages as $languageCode) {
            $this->widgetConfig->save($widgetConfig, $languageCode);
            $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion(
                $languageCode,
            );
            $this->styleBuilder->updateCssFileAndIncrementStyleVersion(
                $this->wpFunction->getCurrentBlogId(),
                $languageCode,
            );
        }

        // Save config for this language. The save routine also updates the current language object.
        $this->widgetConfig->save($widgetConfig, $this->language->getSelectedLanguageCode());
        $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion(
            $this->language->getSelectedLanguageCode(),
        );
        $this->styleBuilder->updateCssFileAndIncrementStyleVersion(
            $this->wpFunction->getCurrentBlogId(),
            $this->language->getSelectedLanguageCode(),
        );
        $this->thirdPartyCacheClearerManager->clearCache();

        $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['savedSuccessfully']);

        return true;
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
        $templateData['localized'] = WidgetLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['data'] = (array) $this->widgetConfig->get();
        $templateData['data']['iabTcf'] = $this->iabTcfConfig->get();
        $templateData['languages'] = $this->language->getLanguageList();

        return $this->template->getEngine()->render(
            'widget/widget.html.twig',
            $templateData,
        );
    }
}
