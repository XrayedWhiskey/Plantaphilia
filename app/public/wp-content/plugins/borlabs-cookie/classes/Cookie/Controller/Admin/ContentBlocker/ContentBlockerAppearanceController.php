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
use Borlabs\Cookie\Localization\ContentBlocker\ContentBlockerAppearanceLocalizationStrings;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\System\Config\ContentBlockerStyleConfig;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\Script\ScriptConfigBuilder;
use Borlabs\Cookie\System\Style\StyleBuilder;
use Borlabs\Cookie\System\Template\Template;
use Borlabs\Cookie\System\ThirdPartyCacheClearer\ThirdPartyCacheClearerManager;

final class ContentBlockerAppearanceController implements ControllerInterface
{
    public const CONTROLLER_ID = 'borlabs-cookie-content-blocker-appearance';

    private ContentBlockerStyleConfig $contentBlockerStyleConfig;

    private GlobalLocalizationStrings $globalLocalizationStrings;

    private Language $language;

    private MessageManager $messageManager;

    private ScriptConfigBuilder $scriptConfigBuilder;

    private StyleBuilder $styleBuilder;

    private Template $template;

    private ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager;

    private WpFunction $wpFunction;

    public function __construct(
        ContentBlockerStyleConfig $contentBlockerStyleConfig,
        GlobalLocalizationStrings $globalLocalizationStrings,
        Language $language,
        MessageManager $messageManager,
        ScriptConfigBuilder $scriptConfigBuilder,
        StyleBuilder $styleBuilder,
        Template $template,
        ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager,
        WpFunction $wpFunction
    ) {
        $this->contentBlockerStyleConfig = $contentBlockerStyleConfig;
        $this->globalLocalizationStrings = $globalLocalizationStrings;
        $this->language = $language;
        $this->messageManager = $messageManager;
        $this->scriptConfigBuilder = $scriptConfigBuilder;
        $this->styleBuilder = $styleBuilder;
        $this->template = $template;
        $this->thirdPartyCacheClearerManager = $thirdPartyCacheClearerManager;
        $this->wpFunction = $wpFunction;
    }

    public function reset(): string
    {
        $this->contentBlockerStyleConfig->save(
            $this->contentBlockerStyleConfig->defaultConfig(),
            $this->language->getSelectedLanguageCode(),
        );
        $this->styleBuilder->updateCssFileAndIncrementStyleVersion(
            $this->wpFunction->getCurrentBlogId(),
            $this->language->getSelectedLanguageCode(),
        );
        $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion(
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
        $contentBlockerStyleConfig = $this->contentBlockerStyleConfig->get();
        $contentBlockerStyleConfig->backgroundColor = $postData['backgroundColor'] ?? '';
        $contentBlockerStyleConfig->backgroundOpacity = (int) $postData['backgroundOpacity'];
        $contentBlockerStyleConfig->borderRadiusBottomLeft = (int) $postData['borderRadiusBottomLeft'];
        $contentBlockerStyleConfig->borderRadiusBottomRight = (int) $postData['borderRadiusBottomRight'];
        $contentBlockerStyleConfig->borderRadiusTopLeft = (int) $postData['borderRadiusTopLeft'];
        $contentBlockerStyleConfig->borderRadiusTopRight = (int) $postData['borderRadiusTopRight'];
        $contentBlockerStyleConfig->buttonBorderRadiusBottomLeft = (int) $postData['buttonBorderRadiusBottomLeft'];
        $contentBlockerStyleConfig->buttonBorderRadiusBottomRight = (int) $postData['buttonBorderRadiusBottomRight'];
        $contentBlockerStyleConfig->buttonBorderRadiusTopLeft = (int) $postData['buttonBorderRadiusTopLeft'];
        $contentBlockerStyleConfig->buttonBorderRadiusTopRight = (int) $postData['buttonBorderRadiusTopRight'];
        $contentBlockerStyleConfig->buttonColor = $postData['buttonColor'] ?? '';
        $contentBlockerStyleConfig->buttonColorHover = $postData['buttonColorHover'] ?? '';
        $contentBlockerStyleConfig->buttonTextColor = $postData['buttonTextColor'] ?? '';
        $contentBlockerStyleConfig->buttonTextColorHover = $postData['buttonTextColorHover'] ?? '';
        $contentBlockerStyleConfig->fontFamily = (string) ($postData['fontFamily'] ?? 'inherit');
        $contentBlockerStyleConfig->fontFamilyStatus = (bool) ($postData['fontFamilyStatus'] ?? false);
        $contentBlockerStyleConfig->fontSize = (int) $postData['fontSize'];
        $contentBlockerStyleConfig->linkColor = $postData['linkColor'] ?? '';
        $contentBlockerStyleConfig->linkColorHover = $postData['linkColorHover'] ?? '';
        $contentBlockerStyleConfig->separatorColor = $postData['separatorColor'] ?? '';
        $contentBlockerStyleConfig->separatorWidth = (int) $postData['separatorWidth'];
        $contentBlockerStyleConfig->textColor = $postData['textColor'] ?? '';
        $contentBlockerStyleConfig->useIndividualStyles = (bool) ($postData['useIndividualStyles'] ?? true);

        // Save config for other languages
        $languages = array_keys(
            array_filter(
                $postData['languages']['configuration'] ?? [],
                fn (string $checked) => $checked === '1',
            ),
        );

        foreach ($languages as $languageCode) {
            $this->contentBlockerStyleConfig->save($contentBlockerStyleConfig, $languageCode);
            $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion(
                $languageCode,
            );
            $this->styleBuilder->updateCssFileAndIncrementStyleVersion(
                $this->wpFunction->getCurrentBlogId(),
                $languageCode,
            );
        }

        // Save config for this language. The save routine also updates the current language object.
        $this->contentBlockerStyleConfig->save(
            $contentBlockerStyleConfig,
            $this->language->getSelectedLanguageCode(),
        );
        $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion(
            $this->language->getSelectedLanguageCode(),
        );
        $this->styleBuilder->updateCssFileAndIncrementStyleVersion(
            $this->wpFunction->getCurrentBlogId(),
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
        $templateData['localized'] = ContentBlockerAppearanceLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['data'] = $this->contentBlockerStyleConfig->get();
        $templateData['languages'] = $this->language->getLanguageList();

        return $this->template->getEngine()->render(
            'content-blocker/content-blocker-appearance/content-blocker-appearance.html.twig',
            $templateData,
        );
    }
}
